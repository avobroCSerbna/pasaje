<?php
// defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('America/Argentina/Buenos_Aires');
class Main extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('main_model');
		$this->load->helper('url_helper');
		$this->load->library('session');

		$logged = $this->session->userdata('is_logged');

		if (!$logged)
			redirect('login');
	}

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		if ($this->main_model->rol($this->session->userdata('usuario'),"pacientes"))
			redirect('main/pacientes');
		else if ($this->main_model->rol($this->session->userdata('usuario'),"turnos"))
			redirect('main/agenda_turnos');
		else if ($this->main_model->rol($this->session->userdata('usuario'),"grupos"))
			redirect('main/agenda_grupos');
		else if ($this->main_model->rol($this->session->userdata('usuario'),"facturacion"))
			redirect('main/facturacion');
		else if ($this->main_model->rol($this->session->userdata('usuario'),"admin"))
			redirect('main/admin');
		else
			$this->error();
	}

	public function error() {
		$data['url'] = base_url('index.php/login/logout');

		// $arraybar = array (
		// 	'admin_act' 		=> "",
		// 	'admin_url' 		=> base_url('index.php/main/admin'),
		// 	'admin_show' 		=> $this->main_model->rol($this->session->userdata('usuario'),"admin"),
		// 	'turnos_act' 		=> "",
		// 	'turnos_url' 		=> base_url('index.php/main/agenda_turnos'),
		// 	'turnos_show' 		=> $this->main_model->rol($this->session->userdata('usuario'),"turnos"),
		// 	'pacientes_act' 	=> "",
		// 	'pacientes_url' 	=> base_url('index.php/main/pacientes'),
		// 	'pacientes_show' 	=> $this->main_model->rol($this->session->userdata('usuario'),"pacientes"),
		// 	'facturacion_act' 	=> "",
		// 	'facturacion_url' 	=> base_url('index.php/main/facturacion'),
		// 	'facturacion_show' 	=> $this->main_model->rol($this->session->userdata('usuario'),"facturacion"),
		// 	'grupos_act' 	=> "",
		// 	'grupos_url' 	=> base_url('index.php/main/agenda_grupos'),
		// 	'grupos_show' 	=> $this->main_model->rol($this->session->userdata('usuario'),"grupos")
		// );
		//
		// $data['navbar'] = $this->create_navbar($arraybar);

		$this->load->view('header', array('title' => "Error"));
			// $this->load->view('navbar', $data);
			$this->load->view('error_view',$data);
		$this->load->view('footer');
	}
	public function create_navbar($array)
	{
		$navbar = "";

		if ($array['admin_show'])
			$navbar .= '<li class="'.$array['admin_act'].'"><a href="'.$array['admin_url'].'"><span class = "glyphicon glyphicon-dashboard"></span> Admin</a></li>';
		if ($array['turnos_show'])
			$navbar .= '<li class="'.$array['turnos_act'].'"><a href="'.$array['turnos_url'].'"><span class = "glyphicon glyphicon-list-alt"></span> Turnos</a></li>';
		if ($array['grupos_show'])
				$navbar .= '<li class="'.$array['grupos_act'].'"><a href="'.$array['grupos_url'].'"><span class = "glyphicon glyphicon-star"></span> Grupos</a></li>';
		if ($array['facturacion_show'])
				$navbar .= '<li class="'.$array['facturacion_act'].'"><a href="'.$array['facturacion_url'].'"><span class = "glyphicon glyphicon-flag"></span> Facturación</a></li>';
		if ($array['pacientes_show'])
			$navbar .= '<li class="'.$array['pacientes_act'].'"><a href="'.$array['pacientes_url'].'"><span class = "glyphicon glyphicon-user"></span> Pacientes</a></li>';


		$navbar .= '<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">Opciones<span class="caret"></span></a>
			<ul class="dropdown-menu">'.
			//   <li><a href="#"><span class = "glyphicon glyphicon-lock"></span> Bloquear</a></li>
			  '<li><a href="'.base_url('index.php/login/logout').'"><span class = "glyphicon glyphicon-log-out"></span> Salir</a></li>
			</ul>
		</li>';

		return $navbar;
	}

	public function agenda_turnos()
	{
		if (!$this->main_model->rol($this->session->userdata('usuario'),"turnos") && !$this->main_model->rol($this->session->userdata('usuario'),"admin"))
			redirect('main');
		else {

			$data['especialista_sel'] = $this->session->userdata('especialista');
			// $data['especialidad_sel'] = $this->session->userdata('especialidad');
			$data['usuario'] = $this->session->userdata('usuario');
			$data['usuarios'] = $this->get_usuarios("todos");//$this->main_model->get_data('usuarios');
			$data['is_admin'] = $this->main_model->rol($this->session->userdata('usuario'),"admin") ? 1 : 0;

			if ($data['especialista_sel'] != "todos") {
					$data['agendas'] = $this->get_agendas($data['especialista_sel']);//$this->main_model->get_data('agendas', array('usuario' => $data['especialista_sel']));
					$data['especialidades'] = $this->get_especialidades($data['especialista_sel']);
			}
			else {
					$data['agendas'] = $this->get_agendas("todos");
					$data['especialidades'] = $this->get_especialidades("todos");
			}

			if ($data['is_admin']) {
				$data["agenda_extra"] = '<h3>Crear Agenda</h3><hr>';
				$data["agenda_extra"] .= $this->load->view('agenda_extra_view', '', true);
			}
			else
				$data["agenda_extra"] = '<div class = "text-muted" style = "font-size:30px;text-align:center;height:150px;padding:50px"><i>No hay agenda abierta para este día</i></div>';

			$arraybar = array (
				'admin_act' 		=> "",
				'admin_url' 		=> base_url('index.php/main/admin'),
				'admin_show' 		=> $this->main_model->rol($this->session->userdata('usuario'),"admin"),
				'turnos_act' 		=> "active",
				'turnos_url' 		=> "#",
				'turnos_show' 		=> true,
				'pacientes_act' 	=> "",
				'pacientes_url' 	=> base_url('index.php/main/pacientes'),
				'pacientes_show' 	=> $this->main_model->rol($this->session->userdata('usuario'),"pacientes"),
				'facturacion_act' 	=> "",
				'facturacion_url' 	=> base_url('index.php/main/facturacion'),
				'facturacion_show' 	=> $this->main_model->rol($this->session->userdata('usuario'),"facturacion"),
				'grupos_act' 	=> "",
				'grupos_url' 	=> base_url('index.php/main/agenda_grupos'),
				'grupos_show' 	=> $this->main_model->rol($this->session->userdata('usuario'),"grupos")
			);

			$navbar['navbar'] = $this->create_navbar($arraybar);

			$this->load->view('header', array('title' => "Agenda Turnos"));
				$this->load->view('navbar', $navbar);
				$this->load->view('agenda_view',$data);
				$this->load->view('modal_turno');
				$this->load->view('modal_confirmacion');
				$this->load->view('modal_datos');
				$this->load->view('modal_cambiar_turno');
				$this->load->view('modal_notas', $data);
				$this->load->view('modal_error');
				$this->load->view('modal_agenda_extra',$data);
			$this->load->view('footer');
		}
	}

	public function admin()
	{
		if (!$this->main_model->rol($this->session->userdata('usuario'),"admin"))
			redirect('main');
		else {
			$data['usuario'] = $this->session->userdata('usuario');
			$data['usuarios'] = $this->get_usuarios("todos");//$this->main_model->get_data("usuarios");
			$data['agendas'] = $this->get_agendas("todos");
			$data["tipos"] = $this->main_model->get_data("tipos_grupos",null,null);
			// $data['especialistas'] = $this->get_especialistas();

			$arraybar = array (
				'admin_act' 		=> "active",
				'admin_url' 		=> "#",
				'admin_show' 		=> true,
				'turnos_act' 		=> "",
				'turnos_url' 		=> base_url('index.php/main/agenda_turnos'),
				'turnos_show' 		=> $this->main_model->rol($this->session->userdata('usuario'),"turnos"),
				'pacientes_act' 	=> "",
				'pacientes_url' 	=> base_url('index.php/main/pacientes'),
				'pacientes_show' 	=> $this->main_model->rol($this->session->userdata('usuario'),"pacientes"),
				'facturacion_act' 	=> "",
				'facturacion_url' 	=> base_url('index.php/main/facturacion'),
				'facturacion_show' 	=> $this->main_model->rol($this->session->userdata('usuario'),"admin"),
				'grupos_act' 	=> "",
				'grupos_url' 	=> base_url('index.php/main/agenda_grupos'),
				'grupos_show' 	=> $this->main_model->rol($this->session->userdata('usuario'),"grupos")
			);

			$navbar['navbar'] = $this->create_navbar($arraybar);

			$this->load->view('header', array('title' => "Admin"));
				$this->load->view('navbar', $navbar);
				$this->load->view('modal_confirmacion');
				$this->load->view('modal_usuario',$data);
				$this->load->view('modal_agenda',$data);
				$this->load->view('modal_grupos',$data);
				$this->load->view('admin_view', $data);
			$this->load->view('footer');
		}
	}

	public function pacientes()
	{

		if (!$this->main_model->rol($this->session->userdata('usuario'),"pacientes") && !$this->main_model->rol($this->session->userdata('usuario'),"admin"))
			redirect('main');
		else {
			$data['usuarios'] = $this->get_usuarios("todos");//$this->main_model->get_data("usuarios");
			$data['agendas'] = $this->get_agendas("todos");

			$arraybar = array (
				'admin_act' 		=> "",
				'admin_url' 		=> base_url('index.php/main/admin'),
				'admin_show' 		=> $this->main_model->rol($this->session->userdata('usuario'),"admin"),
				'turnos_act' 		=> "",
				'turnos_url' 		=> base_url('index.php/main/agenda_turnos'),
				'turnos_show' 		=> $this->main_model->rol($this->session->userdata('usuario'),"turnos"),
				'pacientes_act' 	=> "active",
				'pacientes_url' 	=> "#",
				'pacientes_show' 	=> true,
				'facturacion_act' 	=> "",
				'facturacion_url' 	=> base_url('index.php/main/facturacion'),
				'facturacion_show' 	=> $this->main_model->rol($this->session->userdata('usuario'),"facturacion"),
				'grupos_act' 	=> "",
				'grupos_url' 	=> base_url('index.php/main/agenda_grupos'),
				'grupos_show' 	=> $this->main_model->rol($this->session->userdata('usuario'),"grupos")
			);

			$navbar['navbar'] = $this->create_navbar($arraybar);

			$this->load->view('header', array('title' => "Pacientes"));
				$this->load->view('navbar', $navbar);
				$this->load->view('pacientes_view', $data);
			$this->load->view('footer');
		}

	}

	public function facturacion()
	{
		if (!$this->main_model->rol($this->session->userdata('usuario'),"admin"))
			redirect('main');
		else {

			$data['usuarios'] = $this->get_usuarios("todos");//$this->main_model->get_data("usuarios");
			$data['agendas'] = $this->get_agendas("todos");

			$arraybar = array (
				'admin_act' 		=> "",
				'admin_url' 		=> base_url('index.php/main/admin'),
				'admin_show' 		=> $this->main_model->rol($this->session->userdata('usuario'),"admin"),
				'turnos_act' 		=> "",
				'turnos_url' 		=> base_url('index.php/main/agenda_turnos'),
				'turnos_show' 		=> $this->main_model->rol($this->session->userdata('usuario'),"turnos"),
				'pacientes_act' 	=> "",
				'pacientes_url' 	=> base_url('index.php/main/pacientes'),
				'pacientes_show' 	=> $this->main_model->rol($this->session->userdata('usuario'),"pacientes"),
				'facturacion_act' 	=> "active",
				'facturacion_url' 	=> "#",
				'facturacion_show' 	=> true,
				'grupos_act' 	=> "",
				'grupos_url' 	=> base_url('index.php/main/agenda_grupos'),
				'grupos_show' 	=> $this->main_model->rol($this->session->userdata('usuario'),"agenda_grupos")
			);

			$navbar['navbar'] = $this->create_navbar($arraybar);

			$this->load->view('header', array('title' => "Facturación"));
				$this->load->view('navbar', $navbar);
				$this->load->view('facturacion_view', $data);
			$this->load->view('footer');
		}

	}

	public function agenda_grupos()
	{

		$data['especialista_sel'] = $this->session->userdata('especialista');
		// $data['especialidad_sel'] = $this->session->userdata('especialidad');
		$data['usuario'] = $this->session->userdata('usuario');
		$data['usuarios'] = $this->get_usuarios("todos");//$this->main_model->get_data('usuarios');
		$data['is_admin'] = $this->main_model->rol($this->session->userdata('usuario'),"admin") ? 1 : 0;

		// if ($data['especialista_sel'] != "todos") {
		// 		$data['agendas'] = $this->get_agendas($data['especialista_sel']);//$this->main_model->get_data('agendas', array('usuario' => $data['especialista_sel']));
		// 		$data['especialidades'] = $this->get_especialidades($data['especialista_sel']);
		// }
		// else {
		// 		$data['agendas'] = $this->get_agendas("todos");
		// 		$data['especialidades'] = $this->get_especialidades("todos");
		// }

		if (!$this->main_model->rol($this->session->userdata('usuario'),"grupos") && !$this->main_model->rol($this->session->userdata('usuario'),"admin"))
			redirect('main');
		else {

			// $data['usuarios'] = $this->get_usuarios("todos");//$this->main_model->get_data("usuarios");
			// $data['agendas'] = $this->get_agendas("todos");

			$data['profesor_sel'] = $this->main_model->rol($this->session->userdata('usuario'),"admin") ? "todos" : $this->session->userdata('usuario');
			$data["profesores"] = $this->get_info_completa_grupo("todos",$data['profesor_sel'],"grupos.id_usuario");
			$data["tipos"] = $this->get_info_completa_grupo("todos",$data['profesor_sel'],"grupos.tipo");
			//
			// foreach ($group_data as $key => $value) {
			// 	$data['tipos'][] = $value->tipo;
			// 	$data['profesores'][] = $value->
			// }

			// $data['grupos'] = $this->get_grupos("todos","todos");
			$arraybar = array (
				'admin_act' 		=> "",
				'admin_url' 		=> base_url('index.php/main/admin'),
				'admin_show' 		=> $this->main_model->rol($this->session->userdata('usuario'),"admin"),
				'turnos_act' 		=> "",
				'turnos_url' 		=> base_url('index.php/main/agenda_turnos'),
				'turnos_show' 		=> $this->main_model->rol($this->session->userdata('usuario'),"turnos"),
				'pacientes_act' 	=> "",
				'pacientes_url' 	=> base_url('index.php/main/pacientes'),
				'pacientes_show' 	=> true,
				'facturacion_act' 	=> "",
				'facturacion_url' 	=> base_url('index.php/main/facturacion'),
				'facturacion_show' 	=> $this->main_model->rol($this->session->userdata('usuario'),"facturacion"),
				'grupos_act' 		=> "active",
				'grupos_url' 		=> "#",
				'grupos_show' 		=> $this->main_model->rol($this->session->userdata('usuario'),"grupos")
			);

			$navbar['navbar'] = $this->create_navbar($arraybar);

			$this->load->view('header', array('title' => "Agenda Grupos"));
				$this->load->view('navbar', $navbar);
				$this->load->view('grupos_view', $data);
				$this->load->view('modal_miembro', $data);
				// $this->load->view('modal_datos');
				// $this->load->view('modal_cambiar_turno');
				$this->load->view('modal_notas', $data);
				$this->load->view('modal_confirmacion');
				$this->load->view('modal_error');
			$this->load->view('footer');
		}

	}


/******************************************USUARIOS******************************************/

	public function am_usuario()
	{
		// if ($this->main_model->rol($_POST['usr_usuario'], "especialista"))
		// 	$this->main_model->am_especialista($_POST['usr_usuario']);

		$data = array(
		   	'usuario' 	=> $_POST['usr_usuario'],
		   	'nombre' 	=> ucwords(strtolower($_POST['usr_nombre'])),
		   	'apellido' 	=> ucwords(strtolower($_POST['usr_apellido'])),
			'password'	=> $_POST['usr_usuario'],
		   	'funciones' => json_encode($_POST['usr_funciones'])
		);

		print_r($_POST['usr_funciones']);
		$this->main_model->am_usuario($data);
		// redirect('main/admin#usuarios');
	}

	public function reset_usuario($id)
	{
		$this->main_model->reset_usuario($id);
	}

	public function del_usuario($id)
	{
		// $id = $_POST['id_usuario'];
		$this->main_model->del_usuario($id);
		// redirect('main/admin#usuarios');
	}

	// public function get_usuarios("todos")
	// {
	// 	return $this->main_model->get_data('usuarios');
	// }

	// public function get_usuarios_json()
	// {
	// 	echo json_encode($this->get_usuarios("todos"));
	// }

	public function get_usuarios($id)
	{
		if ($id == "todos")
			$agendas = $this->main_model->get_data("usuarios",null, null);
		else
			$agendas = $this->main_model->get_data("usuarios",null, array('usuario' => $id))[0];

		return $agendas;//$this->get_agendas();

		// return $this->main_model->get_data("usuarios",null,array('usuario' => $id))[0];
	}

	public function get_usuarios_json($id)
	{
		echo json_encode($this->get_usuarios($id));
	}

/******************************************AGENDAS******************************************/

	// public function add_agenda()
	// {
	// 	$this->main_model->add_agenda($_POST);
	// 	redirect('main/admin#especialistas');
	// }

	public function am_agenda()
	{
		// error_reporting(E_ALL); ini_set('display_errors', 1);
		$horarios = array();
		$especialidades = array();

		foreach ($_POST['agenda_dias'] as $key => $dia)
		{
			$horarios[$dia][1] = array(
				"desde"	=>	$_POST[$dia."_desde_man"],
				"hasta" =>	$_POST[$dia."_hasta_man"]
			);

			$horarios[$dia][2] = array(
				"desde"	=>	$_POST[$dia."_desde_tar"],
				"hasta" =>	$_POST[$dia."_hasta_tar"]
			);
		}

		$data = array(
			'id_agenda'	  		=> 	$_POST['agenda_id'],
			'nombre_agenda'		=>	ucwords(strtolower($_POST['agenda_nombre'])),
			'usuario' 			=> 	$_POST['agenda_usuario'],
		  	'especialidad' 		=> 	$_POST['agenda_especialidades'],//json_encode($especialidades),
			'dias_horarios' 	=> 	json_encode($horarios),
		  	'duracion'			=> 	$_POST['agenda_duracion']
		);

		// echo json_encode($data);
		$this->main_model->am_agenda($data);
	}

	public function del_agenda($id)
	{
		// $id = $_POST['id_agenda'];
		$this->main_model->del_agenda($id);
	}

	public function get_agendas($id) //agenda basada en id de usuario especialista
	{
		if ($id == "todos")
			$agendas = $this->main_model->get_data("agendas",null, null);
		else
			$agendas = $this->main_model->get_data("agendas",null, array('usuario' => $id));

		if ($agendas == null)
			$agendas = $this->main_model->get_data("agendas",null, null);

		return $agendas;
	}

	public function get_agenda_json($id) //agenda basada en id de agenda
	{
		if ($id == "todos")
			echo json_encode($this->main_model->get_data("agendas",null, null));
		else
			echo json_encode($this->main_model->get_data("agendas",null, array('id_agenda' => $id))[0]);
	}

	public function get_especialidades($especialista)
	{
		$data = [];

		if ($especialista == "todos")
			$agendas = $this->main_model->get_data("agendas",null, null);
		else
			$agendas = $this->main_model->get_data("agendas",null, array('usuario' => $especialista));

		if ($agendas == null)
			$agendas = $this->main_model->get_data("agendas",null, null);

		foreach ($agendas as $key => $value) {
			$data = array_merge($data, json_decode($value->especialidad));
		}

		return array_unique($data);
	}

	public function get_especialidades_json($especialista)
	{
		echo json_encode($this->get_especialidades($especialista));
	}

	public function get_especialistas($tipo)
	{
		$agendas = $this->get_usuarios("todos");//$this->main_model->get_data("usuarios");

		foreach ($agendas as $key => $value) {
			if (stripos($value->funciones,"especialista") !== false && stripos($value->funciones,$tipo) !== false)
				$data[] = (object) array('usuario' => $value->usuario,
								'nombre' => $value->apellido.', '.$value->nombre[0]
						);
		}

		return $data;
	}

	public function get_especialistas_json($tipo)
	{
		echo json_encode($this->get_especialistas($tipo));
	}

	public function get_agendas_by_esp($esp)
	{
		$esp = str_replace("%20"," ",$esp);

		if ($esp == "todos")
			$where = null;
		else
			$where = array('especialidad' => $esp);

		return $this->main_model->get_data("agendas", $where, null);
	}

	public function get_agendas_by_esp_json($esp)
	{
		echo json_encode($this->get_agendas_by_esp($esp));
	}
	// Horarios de la agenda de un especialista para la fecha en cuestion. No se muestra la agenda de TODOS

	public function get_horarios($id_agenda)
	{
		return $this->main_model->get_horarios($id_agenda);
	}

	public function get_horarios_json($id_agenda)
	{
			echo json_encode($this->main_model->get_horarios($id_agenda));
	}

/******************************************PRUEBAAAAAAAAAA******************************************/
	public function arrange_turnos($turnos, $horarios)
	{

		$result = $horarios;

		if ($turnos != null) {
			if ($horarios == null) {
				$result = $turnos;
			}
			else {
				foreach ($turnos as $index => $val_turno) {

					foreach ($result as $key => $val_hora) {

						if ($val_turno->hora == $val_hora->hora) {
							$result[$key] = $val_turno;
							break;
						}
						else if ($val_turno->hora < $val_hora->hora) {
							array_splice($result, $key, 0, [$val_turno]);
							break;
						}
						else if ($key == count($result)-1){
							$result[] = $val_turno;
							break;
						}

					}

				}
			}
		}
		else {
			$result = [];
		}

		return $result;

	}

	public function get_data_turnos($year, $month, $id_agenda, $esp="")
	{

		$resultado = [];

		$array_dias = array('do', 'lu', 'ma', 'mi', 'ju', 'vi', 'sa');

		$datos_agenda = $this->main_model->get_datos_agenda($id_agenda, $esp);
		$turnos = $this->main_model->get_turnos_mes($year, $month, $datos_agenda); // Turnos del mes para todos o para la agenda seleccionada
		$horarios = $this->main_model->get_horarios($datos_agenda);
		// $horarios = $this->main_model->get_horarios($id_agenda, $esp);
		// $horarios_extra = $this->main_model->get_horarios_extra($year, $month, $id_agenda, $esp);
		$horarios_extra = $this->main_model->get_horarios_extra($year, $month, $datos_agenda);

		foreach ($turnos as $fecha => $datos) {

			$aux = null;
			$dat = $datos;

			if ($id_agenda != "todos") {
				$dow = $array_dias[date('w', strtotime($fecha))];

				if(isset($horarios[$dow])) {
					$aux = $horarios[$dow][0];
				}
				else if (isset($horarios_extra[$fecha])) {
					$aux = $horarios_extra[$fecha][0];
				}

				$dat = $this->arrange_turnos($datos, $aux);
			}

			$resultado[$fecha] = array (
				'datos' => $dat,
				'cant' => sizeof($datos)
			);

		}

		return array(
			'turnos' => $resultado,
			'horarios' => $horarios,
			'horarios_extra' => $horarios_extra
		);

	}

	public function get_data_turnos_json($year, $month, $id_agenda, $esp="")
	{
			echo json_encode($this->get_data_turnos($year, $month, $id_agenda, $esp));
	}

/******************************************TURNOS******************************************/

	public function get_turno($id)
	{
		// $especialista = $this->get_usuarios($turno->especialista);
		$turno = $this->main_model->get_data("turnos",null,array('id_turno' => $id))[0];
		$agenda = $this->main_model->get_data("agendas",null,array('id_agenda' => $turno->agenda))[0];
		$especialista = $this->main_model->get_data("usuarios",null,array('usuario' => $agenda->usuario))[0];
		$paciente = $this->get_paciente($turno->id_paciente);
		$facturacion = $this->main_model->get_data("facturacion_turnos",null,array('id_turno' => $id))[0];

		$turno->name_especialista = $especialista->apellido.', '.$especialista->nombre[0];
		//$turno->especialidades = $this->get_especialidades($turno->especialista);

		$array['datos_paciente'] = $paciente;
		$array['datos_turno'] = $turno;
		$array['datos_facturacion'] = $facturacion;
		return $array;
	}

	public function get_turno_json($id)
	{
		echo json_encode($this->get_turno($id));
	}

	public function nuevo_turno()
	{
		$data = $_POST;

		if ($data['id_paciente'] != "") {
			$paciente = $this->get_paciente($data['id_paciente']);
			$data['dni'] = $paciente->dni;
			$data['direccion'] = $paciente->direccion;
			$data['localidad'] = $paciente->localidad;
			$data['observaciones_paciente'] = $paciente->observaciones;
		}

		$this->am_turno($data);
	}

	public function modificar_datos()
	{
		// $this->am_paciente($_POST);
		$this->am_turno($_POST);
		$this->am_facturacion_turno($_POST);
	}

	public function am_turno($array)
	{
		$id = $this->am_paciente($array);

		// $id = $array['id_paciente'];

		$extra = array();

		if (isset($array['primera_vez']))
			array_push($extra,'primera_vez');

		$usuario = $this->session->userdata('usuario');

		$data_turno = array(
			'id_turno' 		=> $array['id_turno'],
		  	'id_paciente' 	=> $id,
		  	'fecha' 		=> $array['fecha'],
		  	'hora' 			=> $array['hora'],
			'agenda'		=> $array['id_agenda'],
		  	'especialidad' 	=> $array['especialidad'],
			'observaciones' => $array['observaciones_turno'],
			'data_extra'	=> json_encode($extra),
			'estado'		=> $array['estado'],
			'usuario'		=> $usuario
		);

		$this->main_model->am_turno($data_turno);
	}

	public function del_turno()
	{
		$this->main_model->del_turno($_POST['id_turno']);
	}

	public function cambiar_turno()
	{
		$data_turno = array(
			'id_turno' 		=> $_POST['id_turno'],
		   	'fecha' 		=> $_POST['fecha'],
		   	'hora' 			=> $_POST['hora'],
		);

		// echo json_encode($data_turno);
		$this->main_model->cambiar_turno($data_turno);
	}

/******************************************PACIENTES******************************************/

	public function am_paciente($data)
	{
		$tel1 = $this->main_model->join_telefono($data['tel1'], $data['tel2']);
		$tel2 = $this->main_model->join_telefono($data['cel1'], $data['cel2']);

		$data_paciente = array(
			'id_paciente' 	=> $data['id_paciente'],
		  	'nombre' 		=> ucwords(strtolower($data['nombre'])),
		  	'apellido' 		=> ucwords(strtolower($data['apellido'])),
			'dni'			=> isset($data['dni']) ? $data['dni'] : "",
			'direccion'		=> isset($data['direccion']) ? ucwords(strtolower($data['direccion'])) : "",
			'localidad'		=> isset($data['localidad']) ? ucwords(strtolower($data['localidad'])) : "",
			'obra_social'	=> isset($data['obra_social']) ? ucwords(strtolower($data['obra_social'])) : "",
		  	'tel1' 			=> $tel1,
		  	'tel2' 			=> $tel2,
			'observaciones'	=> isset($data['observaciones_paciente']) ? $data['observaciones_paciente'] : "",
			'data_extra'	=> isset($data['data_extra']) ? $data['data_extra'] : "",
		);

		// print_r($data_paciente);
		return $this->main_model->am_paciente($data_paciente);
		// return 0;
	}

	public function get_paciente($id)
	{
		$val = $this->main_model->get_data("pacientes",null,array('id_paciente' => $id));
		if ($val != null)
			return $val[0];
		else
			return null;
	}

	public function get_paciente_json($id)
	{
			// echo json_encode($this->get_paciente($id));
			echo $this->get_paciente($id)->dni;
	}

	public function get_nombre_paciente($id)
	{
		$val = $this->main_model->get_data("pacientes",null,array('id_paciente' => $id));
		if ($val != null)
			return $val[0]->apellido.', '.$val[0]->nombre;
		else
			return null;
	}

	public function autocomplete_pacientes()
	{
		$value = $_POST['query'];

		echo json_encode($this->main_model->get_pacientes_autocomplete(array("apellido" => $value)));
	}

/******************************************NOTAS******************************************/

	public function am_nota()
	{
		$array['id_nota'] = $_POST['id_nota'];
		$array['texto'] = $_POST['texto'];
		$array['usuario'] = $this->session->userdata('usuario');
		$array['destinatario'] = $_POST['destinatario_sel'];
		$array['tipo'] = $_POST['tipo'];
		$array['fecha'] = $_POST['fecha'];

		$this->main_model->am_nota($array);
	}

	public function get_nota($id,$tipo,$fecha="")
	{
		$usr = $this->session->userdata('usuario');

		if ($id == "todas") {
			// $where = "fecha = '".$fecha."' AND (destinatario = 'todos' OR destinatario = '".$usr."' OR usuario = '".$usr."')";
			$where = array("fecha" => $fecha,"tipo" => $tipo);
			$notas = $this->main_model->get_data('notas', null, $where, array("last_update","desc"));

			if ($notas != null) {
				foreach ($notas as $key => $value) {
					$usuario = $this->get_usuarios($value->usuario);
					$value->nombre_usuario = $usuario->apellido.', '.$usuario->nombre[0];
				}
			}
		}
		else {
			$notas = $this->main_model->get_data("notas",null,array('id_nota' => $id))[0];
		}

		return $notas;
	}

	public function get_nota_json($id,$tipo,$fecha="")
	{
		echo json_encode($this->get_nota($id,$tipo,$fecha));
	}

	public function del_nota()
	{
		$this->main_model->del_nota($_POST['id_nota']);
	}

/******************************************FACTURACION******************************************/
	// public function modificar_datos()
	// {
	// 	$this->am_turno($_POST);
	// 	$this->am_facturacion_turno($_POST);
	//
	// 	$data_turno = array(
	// 		'id_turno' 	=> $_POST['id_turno'],
	// 		'estado' 	=> $_POST['estado']
	// 	);
	//
	// 	$this->main_model->change_turno_estado($data_turno);
	//
	// }

	public function am_facturacion_turno($array)
	{

		// $this->am_paciente($_POST);
		//
		// $data_turno = array(
		// 	'id_turno' 	=> $_POST['id_turno'],
		// 	'estado' 	=> $_POST['estado']
		// );
		//
		// $this->main_model->change_turno_estado($data_turno);

		$usuario = $this->session->userdata('usuario');

		$data_extra = array(
			'total' => $array['total'],
			'pago'	=> $array['total'],
			'debe'	=> ""
		);

		$data_facturacion = array(
			'id_facturacion'	=> $array['id_facturacion'],
			'id_turno' 				=> $array['id_turno'],
			'fecha'						=> $array['fecha'],
			'datos'						=> json_encode($data_extra),
			'usuario'					=> $usuario
		);

		if ($array['estado'] == "OK" && $array['total'] != "")
			$this->main_model->am_facturacion_turno($data_facturacion);
		else if ($array['estado'] != "OK" && $array['id_facturacion'] != "")
			$this->main_model->del_facturacion_turno($array['id_facturacion']);

	}

	function am_agenda_extra()
	{

		// $agenda[1] = array();
		// $agenda[2] = array();

		if ($_POST['crear_agenda_desde_man_hora'] != "" && $_POST['crear_agenda_hasta_man_min'] != "" && $_POST['crear_agenda_hasta_man_hora'] != "" && $_POST['crear_agenda_hasta_man_min'] != "") {
			$agenda[1] = array(
				"desde" => $_POST['crear_agenda_desde_man_hora'].":".$_POST['crear_agenda_desde_man_min'],
				"hasta" => $_POST['crear_agenda_hasta_man_hora'].":".$_POST['crear_agenda_hasta_man_min']
			);
		}
		else {
			$agenda[1] = array(
				"desde" => "",
				"hasta" => ""
			);
		}

		if ($_POST['crear_agenda_desde_tar_hora'] != "" && $_POST['crear_agenda_hasta_tar_min'] != "" && $_POST['crear_agenda_hasta_tar_hora'] != "" && $_POST['crear_agenda_hasta_tar_min'] != "") {
			$agenda[2] = array(
				"desde" => $_POST['crear_agenda_desde_tar_hora'].":".$_POST['crear_agenda_desde_tar_min'],
				"hasta" => $_POST['crear_agenda_hasta_tar_hora'].":".$_POST['crear_agenda_desde_tar_min']
			);
		}
		else {
			$agenda[2] = array(
				"desde" => "",
				"hasta" => ""
			);
		}

		$fecha = date('Y-m-d', strtotime($_POST['crear_agenda_fecha']));

		$data = array(
			"id" 				=> $_POST['crear_id'],
			"id_agenda" => $_POST['crear_agenda_id'],
			"fecha" 		=> $fecha,
			"horarios" 	=> json_encode($agenda),
			"duracion" 	=> $_POST['crear_agenda_duracion'],
			"usuario"		=> $this->session->userdata('usuario')
		);

		$this->main_model->am_agenda_extra($data);
	}

	public function get_agenda_extra($agenda, $fecha)
	{
		return $this->main_model->get_agenda_extra($agenda, $fecha);
	}

	public function get_agenda_extra_json($agenda, $fecha)
	{
		echo json_encode($this->get_agenda_extra($agenda, $fecha));
	}

	public function del_agenda_extra()
	{
		$data['agenda'] = $_POST['agenda'];
		$data['fecha'] = $_POST['fecha'];
		$this->main_model->del_agenda_extra($data);
	}

	function amr_test()
	{
		$this->load->view('header', array('title' => "AMR"));
			$this->load->view('amr_test');
		$this->load->view('footer');
	}

	function convenios() {

		// $url = 'https://www.amr.org.ar/gestion/webServices/autorizador/test/profesiones';
		$login = '38026';
		$password = 'IUUIASUX';
		$url = 'https://www.amr.org.ar/gestion/webServices/autorizador/v3/convenios';
		$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
			$result = curl_exec($ch);
		curl_close($ch);

		echo json_encode($result);
	}

	/********************************* GRUPOS *********************************/

	function get_grupos($tipo, $profesor) {

		$result_grupos = $this->main_model->get_info_completa_grupo($tipo, $profesor);
		$grupos = [];

		foreach ($result_grupos as $key => $value) {
			$value->miembros = $this->main_model->get_info_miembros_grupo($value->id_grupo);
			// $value->profesor = $this->get_usuarios($value->id_usuario);
			if (!isset($grupos[$value->dia])) {
				$grupos[$value->dia][] = $value;
			}
			else {
				array_push($grupos[$value->dia], $value);
			}
		}

		return $grupos;
	}

	function get_grupos_json($tipo, $profesor) {
		echo json_encode($this->get_grupos($tipo, $profesor));
	}

	function get_grupo_by_id($id) {
		echo json_encode($this->main_model->get_data("grupos",null, array('id_grupo' => $id))[0]);
	}

	// function get_grupos_by_profesor($profesor) {
	// 	$this->main_model->get_data("grupos",null, array('id_usuario' => $profesor));
	// }

	function get_info_miembros_grupo($id_grupo) {
		return $this->main_model->get_info_miembros_grupo($id_grupo);
	}

	function get_info_completa_grupo($tipo, $profesor, $group="") {
		return $this->main_model->get_info_completa_grupo($tipo, $profesor, $group);
	}

	function get_profesores_tipo_json($tipo) {
		echo json_encode($this->get_info_completa_grupo($tipo,"todos","grupos.id_usuario"));
	}

	function get_tipos_profesor_json($profesor) {
		echo json_encode($this->get_info_completa_grupo("todos",$profesor,"grupos.tipo"));
	}

	function get_data_integrante($integrante) {
		$data['grupos'] = $this->main_model->get_grupos_integrante($integrante);
		$data['integrante'] = $this->get_paciente($integrante);
		return $data;
	}

	function get_data_integrante_json($integrante) {
		echo json_encode($this->get_data_integrante($integrante));
	}

	function guardar_datos() {
		// $array["id_int_grupo"] = $_POST["id_int_grupo"];

		$array["data_extra"] = array(
						"valor_cuota" 		=> 	"",
						"pagado" 			=> 	"",
						"saldo"				=>	"",
						"fecha_ultimo_pago"	=>	"",
						"prox_vencimiento"	=> 	""
					);

		$array["id_paciente"] = $_POST["id_integrante"];

		$array["grupo_1"] = $_POST["grupo_1"];
		$array["grupo_2"] = $_POST["grupo_2"];
		$array["grupo_3"] = $_POST["grupo_3"];

		$array["id_int_grupo_1"] = $_POST["id_int_grupo_1"];
		$array["id_int_grupo_2"] = $_POST["id_int_grupo_2"];
		$array["id_int_grupo_3"] = $_POST["id_int_grupo_3"];

		$array["observaciones_paciente"] = $_POST["observaciones"];
		$array["apellido"] = $_POST["apellido"];
		$array["nombre"] = $_POST["nombre"];
		$array["direccion"] = $_POST["direccion"];
		$array["dni"] = $_POST["dni"];
		$array["obra_social"] = $_POST["obra_social"];
		$array["cel1"] = $_POST["cel1"];
		$array["cel2"] = $_POST["cel2"];
		$array["tel1"] = $_POST["tel1"];
		$array["tel2"] = $_POST["tel2"];

		if ($_POST["concepto"] == "abono") {
			$array["data_extra"] = array(
							"valor_cuota" 		=> 	$_POST["valor"],
							"pagado" 			=> 	$_POST["paga"],
							"saldo"				=>	$_POST["saldo"],
							"fecha_ultimo_pago"	=>	date('Y-m-d'),
							"prox_vencimiento"	=> 	date("Y-m-d", strtotime("+1 month"))
						);
		}
		else {
			if ($array["id_paciente"] != "") {
				$datos_integrante = $this->get_paciente($array["id_paciente"]);
				if ($datos_integrante->data_extra != "") {
					$array["data_extra"] = json_decode($datos_integrante->data_extra);
					if ($_POST["concepto"] == "deuda") {
						$array["data_extra"]->fecha_ultimo_pago = date('Y-m-d');
						$array["data_extra"]->pagado = $_POST["paga"];
						$array["data_extra"]->saldo = $_POST["saldo"];
					}
				}
			}
		}

		$array["data_extra"] = json_encode($array["data_extra"]);
		$id = $this->am_paciente($array);

		$array["id_paciente"] = $id;
		$this->am_miembro_grupo($array);

		if ($_POST["concepto"] != "") {
			$this->main_model->am_facturacion_grupo(array(
						"id_socio" 	=> $id,
						"fecha"		=> date('Y-m-d'),
						"monto"		=> $_POST["paga"],
						"concepto"	=> $_POST["concepto"],
						"usuario"	=> $this->session->userdata('usuario')
			));
		}

	}

	function am_miembro_grupo($data) {

		if ($data['grupo_1'] != "") {
			$array["id_gm"] = $data["id_int_grupo_1"];
			$array['id_grupo'] = $data['grupo_1'];
			$array['id_socio'] = $data['id_paciente'];
			$this->main_model->am_miembro_grupo($array);
		}

		if ($data['grupo_2'] != "") {
			$array["id_gm"] = $data["id_int_grupo_2"];
			$array['id_grupo'] = $data['grupo_2'];
			$array['id_socio'] = $data['id_paciente'];
			$this->main_model->am_miembro_grupo($array);
		}

		if ($data['grupo_3'] != "") {
			$array["id_gm"] = $data["id_int_grupo_3"];
			$array['id_grupo'] = $data['grupo_3'];
			$array['id_socio'] = $data['id_paciente'];
			$this->main_model->am_miembro_grupo($array);
		}

	}

	function del_integrante_grupo($id) {
		$this->main_model->del_integrante_grupo($id);
	}

	function am_grupo() {
		$data = array(
			'id_grupo'			=>	$_POST['agenda_id_grupo'],
		   	'id_usuario' 		=> 	$_POST['agenda_usuario'],
		   	'dia' 				=> 	$_POST['agenda_dia'],
			'horario_desde'		=>	$_POST['agenda_horario_desde'],
			'horario_hasta'		=>	$_POST['agenda_horario_hasta'],
			'tipo'				=>	$_POST['agenda_tipo'],
			'cant_integrantes'	=> 	$_POST['agenda_personas']
		);
		$this->main_model->am_grupo($data);
	}

	function del_grupo($id) {
		$this->main_model->del_grupo($id);
	}

	function get_vencimientos($fecha) {
		echo json_encode($this->main_model->get_vencimientos($fecha));
	}

	function get_facturacion_grupos($fecha_desde, $fecha_hasta) {
		return $this->main_model->get_facturacion_grupos($fecha_desde, $fecha_hasta);
	}

	function get_facturacion_grupos_json() {
		$fecha_desde= isset($_POST['fecha_desde']) ? $_POST['fecha_desde'] : "";
		$fecha_hasta= isset($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : "";
		echo json_encode($this->get_facturacion_grupos($fecha_desde, $fecha_hasta));
	}

	function get_deudores_grupo() {
		$data = $this->main_model->get_data("pacientes",null, null);
		$deudores = [];

		foreach ($data as $key => $value) {
			if ($value->data_extra != "") {
				$data_extra = json_decode($value->data_extra);
				if (isset($data_extra->saldo) && $data_extra->saldo < 0)
					$deudores[] = $value;
			}
		}

		return $deudores;
	}

	function get_deudores_grupo_json() {
		echo json_encode($this->get_deudores_grupo());
	}
}
