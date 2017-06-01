<?php

/*
 * @Author:    Kiril Kirkov
 *  Github:    https://github.com/kirilkirkov
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Clients extends USER_Controller
{

    private $num_rows = 2;
    private $editId;

    public function __construct()
    {
        parent::__construct(); 
        $this->load->model(array('ClientsModel', 'NewInvoiceModel'));
    }

    public function index($page = 0)
    {
        $data = array();
        $head = array();
        $head['title'] = 'Administration - Home';
        $rowscount = $this->ClientsModel->countClients($_GET);
        $data['clients'] = $this->ClientsModel->getClients($this->num_rows, $page);
        $data['linksPagination'] = pagination('user/clients', $rowscount, $this->num_rows, 3);
        $this->render('clients/index', $head, $data);
        $this->saveHistory('Go to clients page');
    }

    public function addClient($id = 0)
    {
        $data = array();
        $head = array();
        $head['title'] = 'Administration - Home';
        $this->editId = $id;
        $this->postChecker();
        if ($id > 0) {
            $result = $this->ClientsModel->getClientInfo($id);
            if (empty($result)) {
                show_404();
            }
            $_POST = $result;
        }
        $this->render('clients/addclient', $head, $data);
        $this->saveHistory('Go to add client page');
    }

    private function postChecker()
    {
        if (isset($_POST['client_name'])) {
            $this->setClient();
        }
    }

    private function setClient()
    {
        $isValid = $this->validateClient();
        if ($isValid === true) {
            $_POST['editId'] = $this->editId;
            $this->NewInvoiceModel->setClient($_POST);
            $this->saveHistory('Add client - ' . $_POST['client_name']);
            redirect(lang_url('user/clients'));
        } else {
            $this->session->set_flashdata('resultAction', $isValid);
            if ($this->editId > 0) {
                redirect(lang_url('user/edit/client/' . $this->editId));
            } else {
                redirect(lang_url('user/add/client'));
            }
        }
    }

    private function validateClient()
    {
        $errors = array();
        if (mb_strlen(trim($_POST['client_name'])) == 0) {
            $errors[] = lang('err_create_client_name');
        }
        if (mb_strlen(trim($_POST['client_address'])) == 0) {
            $errors[] = lang('err_create_client_addr');
        }
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    public function deleteClient($id)
    {
        $this->ClientsModel->deleteClient($id);
        redirect(lang_url('user/clients'));
    }

}