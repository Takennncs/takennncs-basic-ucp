<?php

require_once __DIR__ . '/config.php';

if (!isset($_SESSION['steamid'])) {
    header("Location: index.php");
    exit;
}

$steamid64 = $_SESSION['steamid'];

function steamID64ToSteamID($steamid64) {
    $accountId = bcsub($steamid64, '76561197960265728');
    $Y = bcmod($accountId, '2');
    $Z = bcdiv(bcsub($accountId, $Y), '2');
    return "STEAM_0:$Y:$Z";
}

function steamID64ToSteamID3($steamid64) {
    $accountId = bcsub($steamid64, '76561197960265728');
    return "[U:1:$accountId]";
}

function steamID64ToSteamHex($steamid64) {
    $accountId = bcsub($steamid64, '76561197960265728');
    $prefix = '1100001';
    $hex = '';
    do {
        $last = bcmod($accountId, '16');
        $hex = dechex($last) . $hex;
        $accountId = bcdiv(bcsub($accountId, $last), '16');
    } while (bccomp($accountId, '0') > 0);
    return 'steam:' . $prefix . $hex;
}

function steamHexToSteam64($steamhex) {
    if (strpos($steamhex, 'steam:1100001') !== 0) return null;
    $hex = substr($steamhex, strlen('steam:1100001'));
    $accountId = gmp_init('0x' . $hex);
    return bcadd(gmp_strval($accountId), '76561197960265728');
}

function getSteamProfile($steamid64, $apikey) {
    $url = "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/?key={$apikey}&steamids={$steamid64}";
    $response = @file_get_contents($url);
    if ($response === false) {
        return [
            "personaname" => "Tundmatu kasutaja",
            "avatarfull" => "https://steamcommunity-a.akamaihd.net/public/images/avatars/ee/default_avatar_full.jpg",
            "profileurl" => "#"
        ];
    }
    $data = json_decode($response, true);
    return $data['response']['players'][0] ?? [
        "personaname" => "Tundmatu kasutaja",
        "avatarfull" => "https://steamcommunity-a.akamaihd.net/public/images/avatars/ee/default_avatar_full.jpg",
        "profileurl" => "#"
    ];
}

$steamhex = steamID64ToSteamHex($steamid64);
$max_characters = 2;

$limit = max(1, min(10, (int)$max_characters));

$stmt = $pdo->prepare("SELECT * FROM players WHERE steam = ? LIMIT $limit");
$stmt->execute([$steamhex]);
$characters = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($characters as &$char) {
    if (!empty($char['charinfo'])) {
        $info = json_decode($char['charinfo'], true);
        $char['firstname']  = $info['firstname'] ?? 'Tundmatu';
        $char['lastname']   = $info['lastname'] ?? '';
        $char['profilepic'] = $info['profilepic'] ?? 'https://steamcommunity-a.akamaihd.net/public/images/avatars/ee/default_avatar_full.jpg';
    } else {
        $char['firstname']  = 'Tundmatu';
        $char['lastname']   = '';
        $char['profilepic'] = 'https://steamcommunity-a.akamaihd.net/public/images/avatars/ee/default_avatar_full.jpg';
    }
}
unset($char);
$can_create = count($characters) < $max_characters;

$steamprofile = getSteamProfile($steamid64, $steamauth['apikey']);
$steamprofile['steamid64'] = $steamid64;
$steamprofile['steamid32'] = steamID64ToSteamID($steamid64);
$steamprofile['steamid3']  = steamID64ToSteamID3($steamid64);
$steamprofile['steamhex']  = $steamhex;

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM ucp_users WHERE steamid = ? LIMIT 1");
    $stmt->execute([$steamid64]);
    $db_user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$db_user) {
        $stmt = $pdo->prepare("
            INSERT INTO ucp_users (steamid, steamhex, name, role, registered_at)
            VALUES (?, ?, ?, 'Kasutaja', NOW())
        ");
        $stmt->execute([$steamid64, $steamhex, $steamprofile['personaname']]);

        $user_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT * FROM ucp_users WHERE id = ?");
        $stmt->execute([$user_id]);
        $db_user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (empty($db_user['steamhex'])) {
        $stmt = $pdo->prepare("UPDATE ucp_users SET steamhex = ? WHERE id = ?");
        $stmt->execute([$steamhex, $db_user['id']]);
        $db_user['steamhex'] = $steamhex;
    }

    if ($db_user['name'] === 'Tundmatu kasutaja' || empty($db_user['name'])) {
        $stmt = $pdo->prepare("UPDATE ucp_users SET name = ? WHERE id = ?");
        $stmt->execute([$steamprofile['personaname'], $db_user['id']]);
        $db_user['name'] = $steamprofile['personaname'];
    }

    $_SESSION['user_id'] = $db_user['id'];

    $username  = htmlspecialchars($db_user['name'], ENT_QUOTES, 'UTF-8');
    $user_role = htmlspecialchars($db_user['role'] ?? 'Kasutaja', ENT_QUOTES, 'UTF-8');

    $stmt = $pdo->query("SELECT id, steam FROM playtime WHERE steam LIKE 'steam:%'");
    $oldEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($oldEntries as $entry) {
        $new64 = steamHexToSteam64($entry['steam']);
        if ($new64) {
            $stmtUpd = $pdo->prepare("UPDATE playtime SET steam = ? WHERE id = ?");
            $stmtUpd->execute([$new64, $entry['id']]);
        }
    }

    $stmt = $pdo->prepare("
        SELECT p.steam AS steamid64, p.gametime, u.role, COALESCE(u.name, 'Tundmatu mängija') AS personaname
        FROM playtime p
        LEFT JOIN ucp_users u ON u.steamid = p.steam
        ORDER BY p.gametime DESC
        LIMIT 10
    ");
    $stmt->execute();
    $topPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $unknownSteamIds = [];
    foreach ($topPlayers as $p) {
        if ($p['personaname'] === 'Tundmatu mängija' || $p['personaname'] === 'Tundmatu kasutaja') {
            $unknownSteamIds[] = $p['steamid64'];
        }
    }

    if (!empty($unknownSteamIds)) {
        $chunks = array_chunk($unknownSteamIds, 100);
        foreach ($chunks as $chunk) {
            $ids = implode(',', $chunk);
            $url = "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/?key={$steamauth['apikey']}&steamids={$ids}";
            $response = @file_get_contents($url);
            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['response']['players'])) {
                    foreach ($data['response']['players'] as $player) {
                        $stmtUpd = $pdo->prepare("UPDATE ucp_users SET name = ? WHERE steamid = ?");
                        $stmtUpd->execute([$player['personaname'], $player['steamid']]);
                    }
                }
            }
        }

        $stmt->execute();
        $topPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    error_log("Andmebaasi viga: " . $e->getMessage());
    die("Serveri viga, proovi hiljem uuesti.");
}

$user_playtime_hours = 0;
$user_points = 0;

try {
    $stmt = $pdo->prepare("SELECT gametime, points FROM playtime WHERE steam = ? LIMIT 1");
    $stmt->execute([$steamid64]);
    $playtime = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($playtime) {
        $user_playtime_hours = (float)($playtime['gametime'] ?? 0);
        $user_points         = (int)($playtime['points'] ?? 0);
    }
} catch (Exception $e) {
    error_log("Playtime päring ebaõnnestus: " . $e->getMessage());
}

?>