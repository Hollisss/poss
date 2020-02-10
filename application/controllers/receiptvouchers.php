<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Receiptvouchers  extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Categorie_receiptvoucher', 'Categorie_receiptvoucher');
        if (! $this->user) {
            redirect('login');
        }
        $this->register = $this->session->userdata('register') ? $this->session->userdata('register') : FALSE;
    }

    public function index()
    {
        if ($this->register) {
            $Register = Register::find($this->register);
            $store = Store::find($Register->store_id);
            $this->view_data['storeName'] = $store->name;
            $this->view_data['storeId'] = $store->id;
        } else {
            $this->view_data['stores'] = Store::all();
        }
        $this->view_data['categories'] = Categorie_receiptvoucher::all();
        $this->content_view = 'receiptvouchers/view';
    }

    public function add()
    {
        $config['upload_path'] = './files/receiptvouchers/';
        $config['encrypt_name'] = TRUE;
        $config['overwrite'] = FALSE;
        $config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|zip';
        $config['max_size'] = '2048';

        $this->load->library('upload', $config);
        if ($this->upload->do_upload()) {
            $data = array(
                'upload_data' => $this->upload->data()
            );
            $attachment = $data['upload_data']['file_name'];
            $data = array(
                "date" => $this->input->post('date'),
                "reference" => $this->input->post('reference'),
                "category_id" => $this->input->post('category'),
                "store_id" => $this->input->post('store_id'),
                "amount" => $this->input->post('amount'),
                "note" => $this->input->post('note'),
                "attachment" => $attachment,
                "created_by" => $this->session->userdata('user_id')
            );
            $receiptvouchers = Receiptvoucher::create($data);
            redirect("receiptvouchers", "refresh");
        } else {
            $data = array(
                "date" => $this->input->post('date'),
                "reference" => $this->input->post('reference'),
                "category_id" => $this->input->post('category'),
                "store_id" => $this->input->post('store_id'),
                "amount" => $this->input->post('amount'),
                "note" => $this->input->post('note'),
                "attachment" => "",
                "created_by" => $this->session->userdata('user_id')
            );
            $receiptvouchers = Receiptvoucher::create($data);
            redirect("receiptvouchers", "refresh");
        }
    }

    public function edit($id = FALSE)
    {
        if ($_POST) {
            $receiptvouchers = Receiptvoucher::find($id);

            $config['upload_path'] = './files/receiptvouchers/';
            $config['encrypt_name'] = TRUE;
            $config['overwrite'] = FALSE;
            $config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|zip';
            $config['max_size'] = '2048';

            $this->load->library('upload', $config);
            if ($this->upload->do_upload()) {
                $data = array(
                    'upload_data' => $this->upload->data()
                );
                $attachment = $data['upload_data']['file_name'];
                $data = array(
                    "date" => $this->input->post('date'),
                    "reference" => $this->input->post('reference'),
                    "category_id" => $this->input->post('category'),
                    "store_id" => $this->input->post('store_id'),
                    "amount" => $this->input->post('amount'),
                    "note" => $this->input->post('note'),
                    "attachment" => $attachment,
                    "created_by" => $this->session->userdata('user_id')
                );
                if ($receiptvouchers->attachment !== '') {
                    unlink('./files/receiptvouchers/' . $receiptvouchers->attachment);
                }
                $receiptvouchers->update_attributes($data);
                redirect("receiptvouchers", "refresh");
            } else {
                $data = array(
                    "date" => $this->input->post('date'),
                    "reference" => $this->input->post('reference'),
                    "category_id" => $this->input->post('category'),
                    "store_id" => $this->input->post('store_id'),
                    "amount" => $this->input->post('amount'),
                    "note" => $this->input->post('note'),
                    "created_by" => $this->session->userdata('user_id')
                );
                $receiptvouchers->update_attributes($data);
                redirect("receiptvouchers", "refresh");
            }
        } else {

            $receiptvouchers = Receiptvoucher::find($id);

            $store = $receiptvouchers->store_id == 0 ? FALSE : Store::find($receiptvouchers->store_id);
            $this->view_data['storeName'] = $store ? $store->name : 'Store';
            $this->view_data['stores'] = Store::all();
            $this->view_data['categories'] = Categorie_receiptvoucher::all();

            $this->view_data['receiptvouchers'] = $receiptvouchers;
            $this->content_view = 'receiptvouchers/edit';
        }
    }
}
