<?php
defined('BASEPATH') or exit('direct access script not allowed');

class InputPacking extends CI_Controller
{
	public function index()
	{
		$this->load->view('InputPacking/input_packing_view');
	}

	public function ajax_list()
	{
		$this->load->model('InputPackingModel');
		$rst['data'] = $this->InputPackingModel->get_all_distinct();

		echo json_encode($rst);
	}

	public function ajax_get_by_orc()
	{
		$this->load->model('InputPackingModel');
		$rst['data'] = $this->InputPackingModel->get_by_orc();
		// var_dump($rst);
		echo json_encode($rst);
	}

	public function ajax_check_by_barcode($barcode)
	{
		$this->load->model('OutputSewingDetailModel');
		$this->load->model('InputPackingModel');

		$rst['outputSewingDetail'] = $this->OutputSewingDetailModel->get_by_barcode($barcode);
		$rst['inputPacking'] = $this->InputPackingModel->get_by_barcode($barcode);

		echo json_encode($rst);
	}

	public function ajax_save()
	{
		$this->load->model('InputPackingModel');
		$rst = $this->InputPackingModel->save();

		echo json_encode($rst);
	}
}
