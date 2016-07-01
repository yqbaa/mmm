<?php

/**
 * Class Async_Task_Adapter_LabelList
 * 游戏标签列表索引，异步任务
 * @auth fanch
 */
class Async_Task_Adapter_LabelList extends Async_Task_Base {

    public function updateIndex(Async_Task_Message_Task $task) {
        $gameId = $task->getParams();
        $data = $this->getLabelData($gameId);
        if($data) {
            Resource_Index_LabelList::updateLabelIdx($gameId, $data);
        }
        $this->logger->debug("异步刷新游戏标签列表索引");
    }

    public function removeIndex(Async_Task_Message_Task $task) {
        $gameId = $task->getParams();
        Resource_Index_LabelList::removeLabelIdxItem($gameId);
        $this->logger->debug("异步删除游戏标签列表索引");
    }

    public function updateLabelIndex(Async_Task_Message_Task $task){
        $labelId = $task->getParams('labelId');
        Resource_Index_LabelList::updateLabIdx($labelId);
        $this->logger->debug("异步刷新标签列表索引");
    }

    private function getLabelData($gameId){
        $result = array();
        $data = Resource_Service_GameIdxLabel::getsBy(array('game_id'=>$gameId, 'status'=>1, 'game_status'=>1));
        if($data) {
            foreach ($data as $item) {
                $result[] = $item['label_id'];
            }
        }
        return $result;
    }
}

