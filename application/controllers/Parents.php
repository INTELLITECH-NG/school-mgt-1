<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

    class Parents extends CI_Controller
{
    
    
    function __construct()
    {
        parent::__construct();
		$this->load->database();
        $this->load->library('session');
        /*cache control*/
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }
    
    /***default functin, redirects to login page if no admin logged in yet***/
    public function index()
    {
        if ($this->session->userdata('parent_login') != 1)
            redirect(base_url() . 'index.php?login', 'refresh');
        if ($this->session->userdata('parent_login') == 1)
            redirect(base_url() . 'index.php?parents/dashboard', 'refresh');
    }
    
    /***ADMIN DASHBOARD***/
    function dashboard()
    {
        if ($this->session->userdata('parent_login') != 1)
            redirect(base_url(), 'refresh');
        $page_data['page_name']  = 'dashboard';
        $page_data['page_title'] = get_phrase('parent_dashboard');
        $this->load->view('backend/index', $page_data);
    }
    
    
    /****MANAGE TEACHERS*****/
    function teacher_list($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('parent_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'personal_profile') {
            $page_data['personal_profile']   = true;
            $page_data['current_teacher_id'] = $param2;
        }
        $page_data['teachers']   = $this->db->get('teacher')->result_array();
        $page_data['page_name']  = 'teacher';
        $page_data['page_title'] = get_phrase('manage_teacher');
        $this->load->view('backend/index', $page_data);
    }
    
    
    /***********************************************************************************************************/
    
    
    
    /****MANAGE SUBJECTS*****/
    function subject($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('parent_login') != 1)
            redirect(base_url(), 'refresh');
        
        $parent_profile         = $this->db->get_where('parent', array(
            'parent_id' => $this->session->userdata('parent_id')
        ))->row();
        $parent_class_id        = $parent_profile->class_id;
        $page_data['subjects']   = $this->db->get_where('subject', array(
            'class_id' => $parent_class_id
        ))->result_array();
        $page_data['page_name']  = 'subject';
        $page_data['page_title'] = get_phrase('manage_subject');
        $this->load->view('backend/index', $page_data);
    }
    
    
    
    /****MANAGE EXAM MARKS*****/
    function marks($param1 = '')
    {
        if ($this->session->userdata('parent_login') != 1)
            redirect(base_url(), 'refresh');
        $page_data['student_id'] = $param1;
        $page_data['page_name']  = 'marks';
        $page_data['page_title'] = get_phrase('manage_marks');
        $this->load->view('backend/index', $page_data);
    }
    
    
    /**********MANAGING CLASS ROUTINE******************/
    function class_routine($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('parent_login') != 1)
            redirect(base_url(), 'refresh');
        
        $page_data['student_id'] = $param1;
        $page_data['page_name']  = 'class_routine';
        $page_data['page_title'] = get_phrase('manage_class_routine');
        $this->load->view('backend/index', $page_data);
    }
    
    /******MANAGE OWN PROFILE AND CHANGE PASSWORD***/
    function manage_profile($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('parent_login') != 1)
            redirect(base_url() . 'index.php?login', 'refresh');
        if ($param1 == 'update_profile_info') {
            $data['name']        = $this->input->post('name');
            $data['email']       = $this->input->post('email');
            
            $this->db->where('parent_id', $this->session->userdata('parent_id'));
            $this->db->update('parent', $data);
            $this->session->set_flashdata('flash_message', get_phrase('account_updated'));
            redirect(base_url() . 'index.php?parents/manage_profile/', 'refresh');
        }
        if ($param1 == 'change_password') {
            $data['password']             = $this->input->post('password');
            $data['new_password']         = $this->input->post('new_password');
            $data['confirm_new_password'] = $this->input->post('confirm_new_password');
            
            $current_password = $this->db->get_where('parent', array(
                'parent_id' => $this->session->userdata('parent_id')
            ))->row()->password;
            if ($current_password == $data['password'] && $data['new_password'] == $data['confirm_new_password']) {
                $this->db->where('parent_id', $this->session->userdata('parent_id'));
                $this->db->update('parent', array(
                    'password' => $data['new_password']
                ));
                $this->session->set_flashdata('flash_message', get_phrase('password_updated'));
            } else {
                $this->session->set_flashdata('flash_message', get_phrase('password_mismatch'));
            }
            redirect(base_url() . 'index.php?parents/manage_profile/', 'refresh');
        }
        $page_data['page_name']  = 'manage_profile';
        $page_data['page_title'] = get_phrase('manage_profile');
        $page_data['edit_data']  = $this->db->get_where('parent', array(
            'parent_id' => $this->session->userdata('parent_id')
        ))->result_array();
        $this->load->view('backend/index', $page_data);
    }
}
