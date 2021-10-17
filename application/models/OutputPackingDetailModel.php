<?php
defined("BASEPATH") or exit("direct script access not allowed");

class OutputPackingDetailModel extends CI_Model{
	public function get_by_output_packing_id($id){
		$result = $this->db->get_where('output_packing_detail', array('id_output_packing' => $id));

		return $result->result();
	}

	public function check_by_barcode($barcode){
		$rowCount = $this->db->get_where('output_packing_detail', array('barcode'=>$barcode));

		return $rowCount->num_rows();
	}
}
