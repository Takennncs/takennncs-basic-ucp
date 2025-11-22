local UPDATE_INTERVAL = 60000

MySQL.ready(function()
    MySQL.Async.execute("ALTER TABLE playtime ADD COLUMN IF NOT EXISTS updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP")
end)

CreateThread(function()
    while true do
        Wait(UPDATE_INTERVAL)

        local players = GetPlayers()
        local now = os.time()

        for _, src in ipairs(players) do
            local steam = GetPlayerIdentifierByType(src, 'steam')
            if steam and steam:match("^steam:") then
                local hexPart = steam:sub(12)
                local accountId = tonumber("0x" .. hexPart)
                local steam64 = tostring(accountId + 76561197960265728)

                local sessionSeconds = (GetGameTimer() - (GlobalState[src .. ":join"] or GetGameTimer())) / 1000
                local sessionHours = sessionSeconds / 3600

                local points = math.floor(sessionSeconds / 600)

                MySQL.Async.execute([[
                    INSERT INTO playtime (steam, gametime, points, updated_at)
                    VALUES (?, ?, ?, FROM_UNIXTIME(?))
                    ON DUPLICATE KEY UPDATE
                        gametime = gametime + VALUES(gametime),
                        points = points + ?,
                        updated_at = FROM_UNIXTIME(?)
                ]], { steam64, sessionHours, points, now, points, now })
            end
        end
    end
end)

AddEventHandler('playerConnecting', function()
    local src = source
    Citizen.CreateThread(function()
        Citizen.Wait(5000)
        GlobalState[src .. ":join"] = GetGameTimer()
    end)
end)

AddEventHandler('playerDropped', function()
    local src = source
    GlobalState[src .. ":join"] = nil
end)
