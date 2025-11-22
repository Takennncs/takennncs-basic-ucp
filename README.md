# QBcore - Basic Template

**Loe:**  
Lihtne QBcore template, mis sisaldab PHP login süsteemi ja karakteri valiku võimalust.

---

## Funktsioonid
- PHP login süsteem
- Mitme karakteri valik

---

## Piirangud / NB!
See template **ei sisalda**:  
- Aktiivsuspoodi süsteemi  
- Punktisüsteemi  
- Payment / maksesüsteemi  
- Admin süsteemi  
- Turfi alade süsteemi  
- VIP pakette  
- Täiendavaid auth süsteeme  
- Ticketi süteemi 
- Ja paljut

---

## Installatsioon
1. Ava `qb-multicharacter`
2. Muuda `server/main.lua` vastavalt lua-le.  

---

## Näide: qb-multicharacter (Lisa / createCharacter / muuda)

`server/main.lua`:

```lua
local function UpdateSteamHex(src, citizenid)
    if not src or not citizenid then return end
    local steamHex = nil
    for _, id in ipairs(GetPlayerIdentifiers(src)) do
        if id:find("steam:") then
            steamHex = id
            break
        end
    end
    if steamHex then
        MySQL.update('UPDATE players SET steam = ? WHERE citizenid = ?', {steamHex, citizenid}, function(rowsChanged)
            print(('Steam Hex uuendatud: %s'):format(steamHex))
        end)
    end
end

AddEventHandler('QBCore:Server:PlayerLoaded', function(Player)
    if not Player or not Player.PlayerData then return end
    local src = Player.PlayerData.source
    hasDonePreloading[src] = true
    UpdateSteamHex(src, Player.PlayerData.citizenid)
end)

RegisterNetEvent('qb-multicharacter:server:loadUserData', function(cData)
    local src = source
    if not cData or not cData.citizenid then return end
    if QBCore.Player.Login(src, cData.citizenid) then
        repeat Wait(10) until hasDonePreloading[src]
        QBCore.Commands.Refresh(src)
        loadHouseData(src)
        UpdateSteamHex(src, cData.citizenid)
    end
end)

RegisterNetEvent('qb-multicharacter:server:createCharacter', function(data)
    local src = source
    if not data or not data.cid then return end
    local newData = { cid = data.cid, charinfo = data }

    if QBCore.Player.Login(src, false, newData) then
        repeat Wait(10) until hasDonePreloading[src]
        UpdateSteamHex(src, newData.cid)

        if GetResourceState('qb-apartments') == 'started' and Apartments.Starting then
            SetPlayerRoutingBucket(src, GetPlayerPed(src) + math.random(1,999))
            QBCore.Commands.Refresh(src)
            loadHouseData(src)
            TriggerClientEvent('qb-multicharacter:client:closeNUI', src)
            TriggerClientEvent('apartments:client:setupSpawnUI', src, newData)
            GiveStarterItems(src)
        else
            QBCore.Commands.Refresh(src)
            loadHouseData(src)
            TriggerClientEvent('qb-multicharacter:client:closeNUIdefault', src)
            GiveStarterItems(src)
        end
    end
end
