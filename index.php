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
        //тут делаю insert в таблицу list из данных что выше + беру lastInsertId(из таблицы campaign)
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
//        $listId = $con->prepareExecute('SELECT MAX(id) FROM lists');
//        $lastIdList = $listId->fetchAll(PDO::FETCH_ASSOC);
//        dd($lastIdList);
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
            $listCoutryIns = Query::insert('list_country',['idList', 'idCountry'])->build();
            $listCoutryData = [
                'idList' => $listId,
                'idCountry' => $idCoutry['id']
            ];
            $con->prepareExecute($listCoutryIns, $listCoutryData);
        }




    }
}


