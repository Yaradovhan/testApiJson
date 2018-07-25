<?php

require_once 'config.php';
require_once 'autoload.php';

$content = file_get_contents('testApiJson.json');

$results = json_decode($content, true);

$con = new Mysql;

foreach ($results['campaigns'] as $k => $campaign) {
    $idCamp = $k + 1;
    $icon = $campaign['icon'];
    $name = $campaign['name'];
    $platform = $campaign['platform'];
    $minOsVer = $campaign['minOsVer'];
    //тут делаю insert в таблицу campaign из данных что выше
    $insCamp = Query::insert('campaigns', ['id', 'icon', 'name', 'platform', 'minOsVer'])->build();
    $insDataCamp = [
        'id' => $idCamp,
        'icon' => $icon,
        'name' => $name,
        'platform' => $platform,
        'minOsVer' => $minOsVer
    ];

    $con->prepareExecute($insCamp, $insDataCamp);
    $campId = $con->prepareExecute('SELECT MAX(id) FROM campaigns');
    $campaignsId = $campId->fetch(PDO::FETCH_ASSOC);
    foreach ($campaign['list'] as $list) {
        $listId = $list['id'];
        $payout = $list['payout'];
        $incent = $list['incent'];
        $health = $list['health'];
        $offerLink = $list['offerLink'];
        $notes = $list['notes'];
        $offerName = $list['offerName'];
        $reqDeviceId = $list['reqDeviceId'];
        $userFlow = $list['userFlow'];
        $kpi = $list['kpi'];
        $insLists = 'INSERT INTO lists(id, payout, incent, health, offerLink, notes, offerName, reqDeviceId, kpi, userFlow, campaignsId) VALUES (:id, :payout, :incent, :health, :offerLink, :notes, :offerName, :reqDeviceId, :kpi, :userFlow, :campaignsId)';
        $dataInsLists = [
            'id' => $listId,
            'payout' => $payout,
            'incent' => $incent,
            'health' => $health,
            'offerLink' => $offerLink,
            'notes' => $notes,
            'offerName' => $offerName,
            'reqDeviceId' => $reqDeviceId,
            'kpi' => $kpi,
            'userFlow' => $userFlow,
            'campaignsId' => $campaignsId['MAX(id)'],
        ];

        $con->prepareExecute($insLists, $dataInsLists);
        foreach ($list["countries"] as $count) {
            $selCountry = Query::select('country', ['name'])->where(['name'])->build();
            $dataSel = ['name' => $count];
            $stmt = $con->prepareExecute($selCountry, $dataSel);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($res == false) {
                $insCountry = Query::insert('country', ['name'])->build();
                $dataIns = ['name' => $count];
                $con->prepareExecute($insCountry, $dataIns);
            }

            $selCountryId = Query::select('country', ['id'])->where(['name'])->build();
            $selCountryIdData = ['name' => $count];

            $idCountryPrepare = $con->prepareExecute($selCountryId, $selCountryIdData);
            $idCoutry = $idCountryPrepare->fetch(PDO::FETCH_ASSOC);
            $listCoutryIns = Query::insert('list_country', ['idList', 'idCountry'])->build();
            $listCoutryData = [
                'idList' => $listId,
                'idCountry' => $idCoutry['id']
            ];
            
            $con->prepareExecute($listCoutryIns, $listCoutryData);
        }
        if ($list["caps"] != false) {
            foreach ($list["caps"] as $cap) {
                $selCapsTitles = Query::select('capsTitles', ['id', 'name'])->where(['name'])->build();
                $dataSelCapsTitles = ['name' => $cap['title']];
                $stmt = $con->prepareExecute($selCapsTitles, $dataSelCapsTitles);
                $resCapsTitles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($resCapsTitles == false) {
                    $insCapsTitles = Query::insert('capsTitles', ['name'])->build();
                    $dataInsCapsTitles = ['name' => $cap['title']];
                    $con->prepareExecute($insCapsTitles, $dataInsCapsTitles);
                }

                $selCapsTypes = Query::select('capsTypes', ['id', 'name'])->where(['name'])->build();
                $dataSelCapsTypes = ['name' => $cap['type']];
                $stmt = $con->prepareExecute($selCapsTypes, $dataSelCapsTypes);
                $resCapsTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($resCapsTypes == false) {
                    $insCapsTypes = Query::insert('capsTypes', ['name'])->build();
                    $dataInsCapsTypes = ['name' => $cap['type']];
                    $con->prepareExecute($insCapsTypes, $dataInsCapsTypes);
                }

                $stmt = $con->prepareExecute($selCapsTitles, $dataSelCapsTitles);
                $resCapsTitles = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt = $con->prepareExecute($selCapsTypes, $dataSelCapsTypes);
                $resCapsTypes = $stmt->fetch(PDO::FETCH_ASSOC);

                $insCaps = Query::insert('caps', ['typeId', 'titleId', 'amount', 'listId'])->build();
                $dataInsCaps = [
                    'typeId' => $resCapsTypes['id'],
                    'titleId' => $resCapsTitles['id'],
                    'amount' => $cap['amount'],
                    'listId' => $list['id']
                ];

                $con->prepareExecute($insCaps, $dataInsCaps);
            }
        }

    }
}


