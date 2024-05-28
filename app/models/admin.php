<?php
require_once(DIR . '/config/database.php');
require_once('profile.php');


function getAllUsers($offset, $limit)
{
    if (!isset($_COOKIE['session'])) {
        if (getData($_COOKIE['session'])['role'] !== 0) {
            http_response_code(403);
            return ["err"];
        }
        http_response_code(403);
        return ["err"];
    }
    $conn = createConn();
    $getQuery = "SELECT user_info.*, user_login.role, user_login.ban  FROM user_info, user_login WHERE user_info.username = user_login.username LIMIT ? OFFSET ? ";
    $data = executeQuery($conn, $getQuery, [$limit, $offset]);
    closeConn($conn);
    if ($data) {

        return $data;
    } else {

        return ["err"];
    }
    
}


function getCoutUsers()
{
    if (!isset($_COOKIE['session'])) {
        if (getData($_COOKIE['session'])['role'] !== 0) {
            http_response_code(403);
            return false;
        }
        http_response_code(403);
        return false;
    }
    $conn = createConn();
    $getQuery = "SELECT COUNT(*) FROM user_info";
    $data = executeQuery($conn, $getQuery);
    closeConn($conn);

    if ($data) {
        
        return $data;
    } else {
        return false;
    }
}



function updateRoles($accounts, $newRole)
{
    if (!isset($_COOKIE['session'])) {
        if (getData($_COOKIE['session'])['role'] !== 0) {
            http_response_code(403);
            return false;
        }
        http_response_code(403);
        return false;
    }
    $conn = createConn();
    $success = true;
    $accounts = json_decode($accounts);
    if (!is_array($accounts)) {
        return false;
    }
    try {
        $conn->begin_transaction();
        foreach ($accounts as $account) {
            $updateQuery = "UPDATE user_login SET role = ? WHERE username = ?";
            $updateResult = executeQuery($conn, $updateQuery, [$newRole, $account]);

            if (!$updateResult) {
                $success = false;
                break;
            }
        }
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        return false;

    }
    closeConn($conn);

    return $success;
}

function updateBlock($accounts, $ban)
{
    if (!isset($_COOKIE['session'])) {
        if (getData($_COOKIE['session'])['role'] !== 0) {
            http_response_code(403);
            return false;
        }
        http_response_code(403);
        return false;
    }
    $conn = createConn();
    $success = true;
    $accounts = json_decode($accounts);
    if (!is_array($accounts)) {
        return false;
    }
    try {
        $conn->begin_transaction();
        foreach ($accounts as $account) {
            $updateQuery = "UPDATE user_login SET ban = ? WHERE username = ?";
            $updateResult = executeQuery($conn, $updateQuery, [$ban, $account]);

            if (!$updateResult) {
                $success = false;
                break;
            }
        }
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
    }
    closeConn($conn);

    return $success;
}


function updateTalbe($data)
{
    if (!isset($_COOKIE['session'])) {
        if (getData($_COOKIE['session'])['role'] !== 0) {
            http_response_code(403);
            return false;
        }
        http_response_code(403);
        return false;
    }
    $conn = createConn();

    try {
        $conn->begin_transaction();
        $jsonDecode = json_decode($data, true);
        $deleteQuery = "DELETE FROM `tieu_chi` WHERE `loai`=0";
        executeQuery($conn, $deleteQuery);
        foreach ($jsonDecode['data']  as $item) {
            $updateQuery = "UPDATE `tieu_chuan` SET `content`= ? WHERE `id`= ?";
            executeQuery($conn, $updateQuery, [$item['text'], $item['data']['id']]);

            foreach ($item['children'] as $tieuchi) {
                $id_tieu_chuan = $item['data']['id'];
                $content = $tieuchi['data']['content'];
                $diem = $tieuchi['data']['score'];
                $indexId = $tieuchi['data']['index'];
                $id = $tieuchi['id'];

                $updateQuery = "INSERT INTO `tieu_chi` (`id_tieu_chuan`, `indexId` , `content`, `diem`, `loai`, `username`, `id`) 
                                VALUES (?, ?, ?, ?, 0, null, ?)";

                executeQuery($conn, $updateQuery, [$id_tieu_chuan, $indexId, $content, $diem, $id]);
            }
        }
        $conn->commit();
        closeConn($conn);
        return true;
    } catch (Exception $e) {
        
        $conn->rollback();
        closeConn($conn);
        return false;

    }

}


function getTalbe()
{
    if (!isset($_COOKIE['session'])) {
        if (getData($_COOKIE['session'])['role'] !== 0) {
            http_response_code(403);
            return false;
        }
        http_response_code(403);
        return false;
    }
    $conn = createConn();

    try {
        $conn->begin_transaction();
        $dataReturn = [];
        $updateQuery = "SELECT * FROM `tieu_chuan`";
        $datatieuchuan = executeQuery($conn, $updateQuery);
        foreach ($datatieuchuan as $tieuchuan) {
            $updateQuery = "SELECT `tieu_chi`.*, `tieu_chuan`.content AS 'tentieuchuan' FROM `tieu_chi`, `tieu_chuan` WHERE `tieu_chuan`.id = `tieu_chi`.id_tieu_chuan AND loai = 0 AND `tieu_chuan`.id = ? ORDER BY `tieu_chi`.`indexId`";
            $datatieuchuan = executeQuery($conn, $updateQuery, [$tieuchuan['id']]);
            array_push($dataReturn, [$tieuchuan['id'] => $datatieuchuan]);
        }

        $conn->commit();
        closeConn($conn);

        return $dataReturn;
    } catch (Exception $e) {
        $conn->rollback();
        closeConn($conn);

        return false;

    }
}
