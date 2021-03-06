<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CuttingModel extends CI_Model{
    var $table="cutting";
    var $column_order = array('id_cutting', 'orc', 'style', 'color', 'buyer', 'comm', 'so', 'qty', 'boxes', 'prepare_on');
    var $column_search = array('id_cutting', 'orc', 'style', 'color', 'buyer', 'comm', 'so', 'qty', 'boxes', 'prepare_on');
    var $order = array('id_cutting' => 'asc');
    
    private function _get_datatables_query(){
        $this->db->from($this->table);

        $i = 0;

        foreach ($this->column_search as $item)
        {
            if($_POST['search']['value'])
            {

                if($i===0) // first loop
                {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if(count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order']))
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables()
    {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);

        $this->db->distinct();
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }    

    public function get_all(){
        $this->db->from($this->table);
        $query = $this->db->get();

        return $query->result();
    }

    public function get_by_id($id)
    {
        $this->db->from($this->table);
        $this->db->where('id_cutting',$id);
        $query = $this->db->get();

        return $query->row();
    }

    // public function save($data)
    // {
    //     $this->db->insert($this->table, $data);
    //     return $this->db->insert_id();
    // }

    public function save(){
        if(isset($_POST['dataStrCutting'])){
            $dataStrCutting = $_POST['dataStrCutting'];
            $data = array(
                'orc' => $dataStrCutting['orc'],
                'style' => $dataStrCutting['style'],
                'color' => $dataStrCutting['color'],
                'buyer' => $dataStrCutting['buyer'],
                'comm' => $dataStrCutting['comm'],
                'so' => $dataStrCutting['so'],
                'qty' => $dataStrCutting['qty'],
                'boxes' => $dataStrCutting['boxes'],
                'prepare_on' => date('Y-m-d', strtotime($dataStrCutting['prepare_on'])),
                'date_created' => date('Y-m-d'),
            );

            $this->db->insert($this->table, $data);
            return $this->db->insert_id();
        }
    }

    public function update($where, $data)
    {
        $this->db->update($this->table, $data, $where);
        return $this->db->affected_rows();
    }

    public function delete_by_id($id)
    {
        $this->db->where('id_cutting', $id);
        $this->db->delete($this->table);
    }

    public function get_by_orc($orc){
        $this->db->where("orc", $orc);
        $result = $this->db->get($this->table);

        return $result->result();

    }

    public function delete_by_orc($orc){
        $this->db->trans_strict(FALSE);
        // $this->db->trans_begin();
        $this->db->trans_start();
        
        //update order first
        $this->update_order($orc);
        $resultCutting = $this->get_data_cutting($orc);
        foreach($resultCutting as $rc){
            $this->delete_cutting_detail($rc->id_cutting);
        }
        $this->delete_cutting($orc);
        $this->db->trans_complete();

        if($this->db->trans_status() === FALSE){
            return 'false';
        }else{
            return 'true';
        }
        // if($this->db->trans_status === FALSE){
        //     $this->db->trans_rollback();
        //     // return false;
        //     return 'false';
        // }else{
        //     $this->db->trans_commit();
        //     // return true;
        //     return 'true';
        // }        
    }

    public function update_order($orc){
        $this->db->set('to_cutting', null);
        $this->db->set('tgl_to_cutting', null);
        $this->db->where('orc', $orc);
        $this->db->update('order');
        if($this->db->affected_rows()){
            return false;
        }
        return true;

        // return false;
        // if($this->db->affected_rows()){
        //     return true;
        // }else{
        //     return false;
        // }
    }

    public function get_data_cutting($orc){
        $r = $this->db->get_where('cutting', array('orc' => $orc));
        if($r->num_rows()>0){
            return $r->result();
        }
        return false;
        // return $r->result();
    }

    public function delete_cutting_detail($id){
        $this->db->delete('cutting_detail', array('id_cutting' => $id));
        if($this->db->affected_rows()>0){
            return false;
        }
        return true;
    }
    
    public function delete_cutting($o){
        $this->db->where('orc', $o);
        $this->db->delete('cutting');
        if($this->db->affected_rows()>0){
            return false;
        }
        return true;
    }    
}