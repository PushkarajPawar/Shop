<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once APPPATH.'third_party/PHPMailer/class.phpmailer.php';
require_once APPPATH.'third_party/PHPMailer/class.smtp.php';
class Common_Model extends CI_Model {
    /* common function to get records from the database table */
    public function getRecords($table, $fields = '', $condition = '', $order_by = '', $limit = '', $debug = 0,$group_by='') {
        
        $str_sql = '';
        if (is_array($fields)) { /* $fields passed as array */
            $str_sql.=implode(",", $fields);
        } elseif ($fields != "") { /* $fields passed as string */
            $str_sql .= $fields;
        } else {
            $str_sql .= '*';  /* $fields passed blank */
        }
        
        $this->db->select($str_sql, FALSE);
        if (is_array($condition)) { /* $condition passed as array */
            if (count($condition) > 0) {
                foreach ($condition as $field_name => $field_value) {
                    if ($field_name != '' && $field_value != '') {
                        $this->db->where($field_name, $field_value);
                    }
                }
            }
        } else if ($condition != "") { /* $condition passed as string */
            $this->db->where($condition);
        }
        if ($limit != "") {
            $this->db->limit($limit); /* limit is not blank */
        }
        if (is_array($order_by)) {
            $this->db->order_by($order_by[0], $order_by[1]);  /* $order_by is not blank */
        } else if ($order_by != "") {
            $this->db->order_by($order_by);  /* $order_by is not blank */
        }
        if($group_by !=''){
            $this->db->group_by($group_by); 
        }
        $this->db->from($table);  /* getting record from table name passed */
        $query = $this->db->get();
        if ($debug) {
            die($this->db->last_query());
        }
        
        return $query->result_array();
    }

    /* unction to insert record into the database */
    public function insertRow($insert_data, $table_name) {
        $this->db->insert($table_name, $insert_data);
//        echo $this->db->last_query();die;
        return $this->db->insert_id();
    }

    /* function to update record in the database
     * Modified by Arvind	
     */

    public function updateRow($table_name, $update_data, $condition) {

        if (is_array($condition)) {
            if (count($condition) > 0) {
                foreach ($condition as $field_name => $field_value) {
                    if ($field_name != '' && $field_value != '' && $field_value != NULL) {
                        $this->db->where($field_name, $field_value);
                    }
                }
            }
        } else if ($condition != "" && $condition != NULL) {
            $this->db->where($condition);
        }
       $is_updated = $this->db->update($table_name, $update_data);
       
       if($is_updated){
            return 'success';
        }else{
            return 'fail';
        }
    }

    /* common function to delete rows from the table
     * Modified by Arvind
     */

    public function deleteRows($arr_delete_array, $table_name, $field_name) {
        if (count($arr_delete_array) > 0) {
            foreach ($arr_delete_array as $id) {
                if ($id) {
                    $this->db->where($field_name, $id);
                    $query = $this->db->delete($table_name);
                }
            }
        }

        $error = $this->db->_error_message();
        $error_number = $this->db->_error_number();
        if ($error) {
            $controller = $this->router->fetch_class();
            $method = $this->router->fetch_method();
            $error_details = array(
                'error_name' => $error,
                'error_number' => $error_number,
                'model_name' => 'common_model',
                'model_method_name' => 'deleteRows',
                'controller_name' => $controller,
                'controller_method_name' => $method
            );
            $this->common_model->errorSendEmail($error_details);
            redirect(base_url() . 'page-not-found');
            exit();
        }
    }

    /*
     * function to get absolute path for project
     */

    public function absolutePath($path = '') {
        $abs_path = str_replace('system/', $path, BASEPATH);
        //Add a trailing slash if it doesn't exist.
        $abs_path = preg_replace("#([^/])/*$#", "\\1/", $abs_path);
        $error = $this->db->_error_message();
        $error_number = $this->db->_error_number();
        if ($error) {
            $controller = $this->router->fetch_class();
            $method = $this->router->fetch_method();
            $error_details = array(
                'error_name' => $error,
                'error_number' => $error_number,
                'model_name' => 'common_model',
                'model_method_name' => 'absolutePath',
                'controller_name' => $controller,
                'controller_method_name' => $method
            );
            $this->common_model->errorSendEmail($error_details);
            redirect(base_url() . 'page-not-found');
            exit();
        }
        return $abs_path;
    }
    
     public function deleteSingleRecord($id,$table_name,$field_name) {
        if ($id) {
            $this->db->where($field_name, $id);
            $query = $this->db->delete($table_name);
            return $query;
        }
    }

    public function getSingleRow($id,$table_name,$field_name) {
       
        if ($id) {
            
            $this->db->select('*');
            $this->db->from($table_name);
            $this->db->where('user_id',$id);
        
            $query = $this->db->get();
            // echo $this->db->last_query(); die;
            return $query->result_array();
        }
    }
    
    public function deleteInterestedlistRecord($user_id,$event_id) {
        if ($user_id !='' && $event_id !='') {
            $this->db->where('user_id_fk', $user_id);
            $this->db->where('event_id_fk', $event_id);
            $query = $this->db->delete('mst_event_interest');
            if($query){
                return 'success';
            }else{
                return 'fail';
            }
        }
    }
    
      public function setCronUpMsg($emailId='',$message='',$subject=''){
            
                 $currentDate = date("Y-m-d");
                 $Y = date("Y");
                 $previousY = (date("Y") -1);
                 $copyRights = ($previousY."-".$Y);
            
                 $Email="suptelemerge345@gmail.com";
                 $pass="tele123_sup";

                
                 $mail = new PHPMailer(true);
                 $mail->IsSMTP(); // telling the class to use SMTP
                 $mail->SMTPOptions = [
                     'ssl'=> [
                        'verify_peer' => false,
                     'verify_peer_name' => false,
                    'allow_self_signed' => true
                  ]
                 ];
                
                 $mail->SMTPAuth = true;                  // enable SMTP authentication
                 $mail->SMTPSecure = "ssl";
                 $mail->Host = "smtp.gmail.com"; // SMTP server
                 $mail->SMTPKeepAlive = true;
                
                   
                 $mail->Port = 465;                    // set the SMTP port for the GMAIL server
                 $mail->IsHTML(true);
                 $mail->CharSet    ="UTF-8";
                 $mail->ContentType  = "text/html";
                 $mail->Username = $Email; // SMTP account username
                 $mail->Password = $pass;        // SMTP account password
                 $mail->FromName = 'Snehal Patil';
                 $mail->AddAddress($emailId);
                 $mail->From = $Email;
                 $mail->Subject = $subject;                
                 $message = $message;

                 $mail->AltBody = $message;
                 $mail->MsgHTML($message);
                 $mail->Send();
                
        }
        
        function isMobileDevice() {
            return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
        }
    
    /* function to writer serialize data to file */
}
