<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Categorie_receiptvouchers extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Categorie_receiptvoucher', 'Categorie_receiptvoucher');
        if (! $this->user) {
            redirect('login');
        }
    }

    public function index()
    {
        $this->view_data['categories'] = Categorie_receiptvoucher::all();
        $this->content_view = 'categorie_receiptvouchers/view';
    }

    public function add($page = FALSE)
    {
		// i added a page variable to know that this process comes from expences page to redirect the user to, but if he add it from expence_categorie page it will redirected to categorie_expences page
        Categorie_receiptvoucher::create($_POST);
        if($page=="receiptvouchers") {
			redirect("receiptvouchers", "refresh");
        } else {
			redirect("categorie_receiptvouchers", "refresh");
		}
    }

    public function edit($id = FALSE)
    {
        if ($_POST) {
            $category = Categorie_receiptvoucher::find($id);
            $category->update_attributes($_POST);
            redirect("categorie_receiptvouchers", "refresh");
        } else {
            $this->view_data['category'] = Categorie_receiptvoucher::find($id);
            $this->content_view = 'categorie_receiptvouchers/edit';
        }
    }

    public function delete($id)
    {
        $category = Categorie_receiptvoucher::find($id);
        $category->delete();
        redirect("categorie_receiptvouchers", "refresh");
    }
}
