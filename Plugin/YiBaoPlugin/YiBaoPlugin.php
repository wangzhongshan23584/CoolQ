<?php

/**
 * Created by PhpStorm.
 * User: Slight
 * Date: 2017/5/5
 * Time: 14:43
 */
class YiBaoPlugin extends Plugin
{

    public function Start()
    {
        $post_type = $this->getGetData()['post_type'];
        $message_type = $this->getGetData()['message_type'];
        $user_id = $this->getGetData()['user_id'];
        $message = $this->getGetData()['message'];
        $Robot = $this->getRobot();
        switch ($post_type) {
            case "message":
                $url = "http://www.kilingzhang.com/Api/YiBao/api.php?role=" . Role . "&hash=" . Hash . "&user_id=$user_id&text=" . urlencode($message);
                $json = file_get_contents($url);
                $data = json_decode($json, true);
                $msg = isset($data['data']) ? $data['data'] : "";
                if ($msg != null) {
                    if (!empty($data) && $data['code'] != 0) {
                        $msg = addslashes($json);
                    } else {
                        $msg = $data['data'];
                    }
                    switch ($message_type) {
                        case "private":
                            $sub_type = $this->getGetData()['sub_type'];
                            switch ($sub_type) {
                                case "friend":
                                    $Robot->sendPrivateMsg($user_id, CoolQ::deCodeHtml(addslashes($msg)));
                                    break;
                                case "group":

                                    break;
                                case "discuss":

                                    break;
                                case "other":

                                    break;
                            }
                            break;
                        case "group":
                            $group_id = $this->getGetData()['group_id'];
                            $pro = explode("功能", $message);
                            if (count($pro) >= 2) {
                                $Robot->sendGroupMsg($group_id, CoolQ::deCodeHtml("[CQ:at,qq=$user_id] \n" . $msg));
                            } else {
                                $Robot->sendPrivateMsg($user_id, CoolQ::deCodeHtml($msg));
                                $Robot->sendGroupMsg($group_id, CoolQ::deCodeHtml("[CQ:at,qq=$user_id]" . "亲,查询结果已经私发给你了~"));
                            }
                            break;
                        case "discuss":
                            $discuss_id = $this->getGetData()['discuss_id'];
                            $Robot->sendDiscussMsg($discuss_id, CoolQ::deCodeHtml($data['data']));

                            break;
                    }
                }
                break;
        }
        if ($msg != null && $msg != "") {
            $this->setIntercept(true);
        }
    }

}