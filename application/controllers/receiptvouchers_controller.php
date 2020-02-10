<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Receiptvouchers_controller extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('receiptvoucher_model', 'receiptvoucher');
        $this->load->model('categorie_receiptvoucher', 'categorie_receiptvoucher');
        $this->user = $this->session->userdata('user_id') ? User::find_by_id($this->session->userdata('user_id')) : FALSE;
        $lang = $this->session->userdata("lang") == null ? "english" : $this->session->userdata("lang");
        $this->lang->load($lang, $lang);

        $this->setting = Setting::find(1);
    }

    public function ajax_list()
    {
      date_default_timezone_set($this->setting->timezone);
        $list = $this->receiptvoucher->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $receiptvoucher) {
            $no ++;
            $row = array();
            $row[] = $receiptvoucher->date;
            $row[] = $receiptvoucher->reference;
            $row[] = number_format((float)$receiptvoucher->amount, $this->setting->decimals, '.', '');
            try{$category = Categorie_receiptvoucher::find($receiptvoucher->category_id)->name;}catch (\Exception $e){$category = "-";}
            $row[] = $category;
            try{$store = Store::find($receiptvoucher->store_id)->name;}catch (\Exception $e){$store = "-";}
            $row[] = $store;
            try{$username = User::find($receiptvoucher->created_by)->username;}catch (\Exception $e){$username = "-";}
            $row[] = $username;

            // add html for action
            if ($this->user->role === "admin")
                $row[] = '<div class="btn-group">
                      <a class="btn btn-default" href="javascript:void(0)" onclick="delete_receiptvouchers(' . $receiptvoucher->id . ')" title="' . label("Delete") . '"><i class="fa fa-times"></i></a>
                      <a class="btn btn-default" href="receiptvouchers/edit/' . $receiptvoucher->id . '" title="' . label("Edit") . '"><i class="fa fa-pencil"></i></a>
                      ' . ($receiptvoucher->attachment ? '<a class="btn color02 white open-modalimage" target="_blank" href="' . site_url() . 'files/receiptvouchers/' . $receiptvoucher->attachment . '" title="' . label("ViewFile") . '"><i class="fa fa-file-archive-o"></i></a>' : '') . '
                    </div>';
            else
                $row[] = '<div class="btn-group"><a class="btn btn-default" href="receiptvouchers/edit/' . $receiptvoucher->id . '" title="' . label("Edit") . '"><i class="fa fa-pencil"></i></a>
                      ' . ($receiptvoucher->attachment ? '<a class="btn color02 white open-modalimage" target="_blank" href="' . site_url() . 'files/receiptvouchers/' . $receiptvoucher->attachment . '" title="' . label("ViewFile") . '"><i class="fa fa-file-archive-o"></i></a>' : '') . '
                    </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->receiptvoucher->count_all(),
            "recordsFiltered" => $this->receiptvoucher->count_filtered(),
            "data" => $data
        );
        // output to json format
        echo json_encode($output);
    }

    public function ajax_delete($id)
    {
        $receiptvoucher = receiptvoucher::find($id);
        if ($receiptvoucher->attachment !== '') {
            unlink('./files/receiptvouchers/' . $receiptvoucher->attachment);
        }
        $this->receiptvoucher->delete_by_id($id);
        echo json_encode(array(
            "status" => TRUE
        ));
    }
}
