<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Person extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('person_model', 'person');
    }

    public function index() {
        $this->load->helper('url');
        $this->load->view('person_view');
    }

    public function ajax_list() {
        $list = $this->person->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $person) {
            $no++;
            $row = array();
            $row[] = $person->name;
            $row[] = $person->dob;
            $row[] = $person->gender;
            $row[] = $person->type_of_service;
            $row[] = $person->general_observations;


            //add html for action
            $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_person(' . "'" . $person->id . "'" . ')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_person(' . "'" . $person->id . "'" . ')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->person->count_all(),
            "recordsFiltered" => $this->person->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function ajax_edit($id) {
        $data = $this->person->get_by_id($id);
        $data->dob = ($data->dob == '0000-00-00') ? '' : $data->dob;
        echo json_encode($data);
    }

    public function ajax_add() {
        $this->_validate();
        $data = array(
            'name' => $this->input->post('name'),
            'dob' => $this->input->post('dob'),
            'gender' => $this->input->post('gender'),
            'type_of_service' => $this->input->post('type_of_service'),
            'general_observations' => $this->input->post('general_observations'),
        );
        $insert = $this->person->save($data);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_update() {
        $this->_validate();
        $data = array(
            'name' => $this->input->post('name'),
            'dob' => $this->input->post('dob'),
            'gender' => $this->input->post('gender'),
            'type_of_service' => $this->input->post('type_of_service'),
            'general_observations' => $this->input->post('general_observations'),
        );
        $this->person->update(array('id' => $this->input->post('id')), $data);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_delete($id) {
        $this->person->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

    private function _validate() {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('name') == '') {
            $data['inputerror'][] = 'name';
            $data['error_string'][] = 'Name is required';
            $data['status'] = FALSE;
        }


        if ($this->input->post('dob') == '') {
            $data['inputerror'][] = 'dob';
            $data['error_string'][] = 'Date of Birth is required';
            $data['status'] = FALSE;
        }

        if ($this->input->post('gender') == '') {
            $data['inputerror'][] = 'gender';
            $data['error_string'][] = 'Please select gender';
            $data['status'] = FALSE;
        }

        if ($this->input->post('type_of_service') == '') {
            $data['inputerror'][] = 'type_of_service';
            $data['error_string'][] = 'Type of Service is required';
            $data['status'] = FALSE;
        }


        if ($this->input->post('general_observations') == '') {
            $data['inputerror'][] = 'general_observations';
            $data['error_string'][] = 'Observation is required';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

}
