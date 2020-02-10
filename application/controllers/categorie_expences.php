<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Categorie_expences extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('categorie_expence', 'Categorie_expence');
        if (! $this->user) {
            redirect('login');
        }
    }

    public function index()
    {
        $this->view_data['categories'] = Categorie_expence::all();
        $this->content_view = 'categorie_expence/view';
    }

    public function add($page = FALSE)
    {
		// i added a page variable to know that this process comes from expences page to redirect the user to, but if he add it from expence_categorie page it will redirected to categorie_expences page
        Categorie_expence::create($_POST);
        if($page=="expences") {
			redirect("expences", "refresh");
        } else {
			redirect("categorie_expences", "refresh");
		}
    }

    public function edit($id = FALSE)
    {
        if ($_POST) {
            $category = Categorie_expence::find($id);
            $category->update_attributes($_POST);
            redirect("categorie_expences", "refresh");
        } else {
            $this->view_data['category'] = Categorie_expence::find($id);
            $this->content_view = 'categorie_expence/edit';
        }
    }

    public function delete($id)
    {
        $category = Categorie_expence::find($id);
        $category->delete();
        redirect("categorie_expences", "refresh");
    }
}
