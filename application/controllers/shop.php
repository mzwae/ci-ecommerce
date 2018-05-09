<?php

class Shop extends MY_Controller
{
    public function __construct()
    {
        parent::construct();
        $this->load->library('cart');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('security');
        $this->load->model('Shop_model');
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
    }

    public function index()
    {
        if (!$this->uri->segment(3)) {
            $data['query'] = $this->Shop_model->get_all_products();
        } else {
            $data['query'] = $this->Shop_model->get_all_products_by_category_name($this->uri->segment(3));
        }

        $data['$cat_query'] = $this->Shop_model->get_all_categories();
        $cart_contents = $this->session->userdata('$cart_contents');
        $data['items'] = $cart_contents['total_items'];

        $this->load->view('templates/header', $data);
        $this->load->view('shop/display_products', $data);
        $this->load->view('templates/footer');
    }

    // Add items to cart
    public function add(){
      $product_id = $this->uri->segment(3);
      $query = $this->Shop_model->get_product_details($product_id);
      foreach ($query->result() as $row) {
        $data = array(
          'id' => $row->product_id,
          'qty' => 1,
          'price' => $row->product_price,
          'name' => $row->product_name
        );
      }

      $this->cart->insert($data);

      $data['cat_query'] = $this->Shop_model->get_all_categories();
      $cart_contents = $this->session->userdata('$cart_contents');
      $data['items'] = $cart_contents['total_items'];

      $this->load->view('templates/header', $data);
      $this->load->view('shop/display_products', $data);
      $this->load->view('templates/footer');
    }
}
