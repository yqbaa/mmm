<?php
/**在线游戏*/
class Async_Task_Adapter_WebGame extends Async_Task_Base {

    public function reservedGame(Async_Task_Message_Task $task) {
        $gameId = $task->getParams();
        $gameInfo = Resource_Service_Games::getBy(array('id'=>$gameId));
        Webgame_Service_ReservedProcess::updateGameInfo($gameInfo['package'], $gameId);
    }

    public function closeReservedActivity(Async_Task_Message_Task $task) {
        $gameId = $task->getParams();
        Webgame_Service_ReservedActivity::updateBy(array('status'=>0), array('game_id'=>$gameId));
        Game_Api_WebRecommendBanner::updateClientBannerCacheData();
    }

    public function closeTestActivity(Async_Task_Message_Task $task) {
        $gameId = $task->getParams();
        $activities = Webgame_Service_TestGames::getsBy(array('game_id'=>$gameId, 'status'=>1));
        if($activities) {
            $activities = Common::resetKey($activities, 'id');
            $activityIds = array_keys($activities);
            Webgame_Service_TestProcess::closeActivity($activityIds);
        }
    }
}