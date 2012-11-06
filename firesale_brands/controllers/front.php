<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Front extends Public_Controller
{

	public $user    = FALSE;
	public $perpage = 15;

	public function __construct()
	{

		parent::__construct();

		// Add data array
		$this->data = new stdClass();

		// Load models, lang, libraries, etc.
		$this->load->driver('Streams');
		$this->load->library('files/files');
		$this->load->model('firesale/routes_m');
		$this->load->model('brands_m');
		$this->load->model('firesale/categories_m');
		$this->load->model('firesale/products_m');

		// Get perpage option
		$this->perpage = $this->settings->get('firesale_perpage');

	}
	
	public function index($id_slug, $page = 0)
	{
			
		// Variables
		$brand = $this->brands_m->get($id_slug);

		// Check it was found
		if( $brand )
		{

			// Build route
			$route = $this->routes_m->build_url('brand', $brand['id']);

			// Get products
			$products = $this->brands_m->get_products($brand['id'], $this->perpage, $page);

			// Build pagination
			$count      = $this->brands_m->get_count($brand['id']);
			$pagination = create_pagination($route.'/',  $count, $this->perpage, 3);

			// Assign data
			$this->data->brand      = $brand;
			$this->data->products   = $products;
			$this->data->pagination = $pagination;

			// Add page content
			$this->template->title($brand['title'])
						   ->set_breadcrumb($brand['title'], $this->routes_m->build_url('brand', $brand['id']))
						   ->set($this->data);

			// Fire events
			Events::trigger('page_build', $this->template);

			// Build page
			$this->template->build('index');

		}
		else
		{
			show_404();
		}

	}

}