<?php

class Shop extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
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

        $data['cat_query'] = $this->Shop_model->get_all_categories();
        $cart_contents = $this->session->userdata('cart_contents');
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
      $cart_contents = $this->session->userdata('cart_contents');
      $data['items'] = $cart_contents['total_items'];

      $this->load->view('templates/header', $data);
      $this->load->view('shop/display_cart', $data);
      $this->load->view('templates/footer');
    }


    public function update_cart(){
      $data = array();
      $i = 0;
      foreach ($this->input->post() as $item) {
        $data[$i]['rowid'] = $item['rowid'];
        $data[$id]['qty'] = $item['qty'];
        $i++;
      }

      $this->cart->update($data);
      redirect('shop/display_cart');

    }

    public function display_cart(){
      $data['cat_query'] = $this->Shop_model->get_all_categories();
      $cart_contents = $this->session->userdata('cart_contents');
      $data['items'] = $cart_contents['total_items'];

      $this->load->view('templates/header', $data);
      $this->load->view('shop/display_cart', $data);
      $this->load->view('templates/footer');
    }

    public function clear_cart(){
      $this->cart->destroy();
      redirect('index');
    }

    // displays form to get user details and convert cart to order
    public function user_details(){
      // Set validation rules
      $this->form_validation->set_rules('first_name', 'Fist Name', 'required');
      $this->form_validation->set_rules('last_name', 'Last Name', 'required');
      $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
      $this->form_validation->set_rules('email_confirm', 'Confirm Email', 'required|valid_email|matches[email]');
      $this->form_validation->set_rules('payment_address', 'Payment Address', 'required');
      $this->form_validation->set_rules('delivery_address', 'Delivery Address', 'required');

      if ($this->form_validation->run() == false) {
        $data['first_name'] = array(
          'name' => 'first_name',
          'class' =>'form-control',
          'id' => 'first_name',
          'value' => set_value('first_name', ''),
          'maxlength' => '100',
          'size' =>'35',
          'placeholder' => 'First Name'
        );
        $data['last_name'] = array(
          'name' => 'last_name',
          'class' =>'form-control',
          'id' => 'last_name',
          'value' => set_value('last_name', ''),
          'maxlength' => '100',
          'size' =>'35',
          'placeholder' => 'Last Name'
        );
        $data['email'] = array(
          'name' => 'email',
          'class' => 'form-control',
          'id' => 'email',
          'value' => set_value('email', ''),
          'maxlength' => '100',
          'size' => '35',
          'placeholder' =>'Email'
        );
        $data['email_confirm'] = array(
          'name' => 'email_confirm',
          'class' => 'form-control',
          'id' => 'email_confirm',
          'value' => set_value('email_confirm', ''),
          'maxlength' => '100',
          'size' => '35',
          'placeholder' => 'Confirm Email'
        );
        $data['payment_address'] = array(
          'name' => 'payment_address',
          'class' => 'form-control',
          'id' => 'payment_address',
          'value'=> set_value('payment_address', ''),
          'maxlength' => '100',
          'size' => '35',
          'placeholder' => 'Payment Address'
        );

        $data['delivery_address'] = array(
          'name' => 'delivery_address',
          'class' => 'form-control',
          'id' => 'delivery_address',
          'value' => set_value('delivery_address', ''),
          'maxlength' => '100',
          'size' => '35',
          'placeholder' => 'Delivery Address'
        );
        $cart_contents = $this->session->userdata('cart_contents');
        $data['items'] = $cart_contents['total_items'];

        $this->load->view('templates/header', $data);
        $this->load->view('shop/user_details', $data);
        $this->load->view('templates/footer');
      } else { // no errors in the form
        $cust_data = array(
          'cust_first_name' => $this->input->post('first_name'),
          'cust_last_name' => $this->input->post('last_name'),
          'cust_email' => $this->input->post('email'),
          'cust_address' => $this->input->post('payment_address')
        );

        // Type of hook that you can use for payment processing
        $payment_code = mt_rand();

        // Save cart data to the $order_data array
        $order_data = array(
          'order_details' => serialize($this->cart->contents()),
          'order_delivery_address' => $this->input->post('delivery_address'),
          'order_closed' => '0',
          'order_fulfilment_code' => $payment_code,
          'order_delivery_address' => $this->input->post('payment_address')
        );

        if ($this->Shop_model->save_cart_to_database($cust_data, $order_data)) {
          echo "User Details Saved Successfully";
        } else {
          echo "User Details Saving Error!";
        }

      }


    }


}
