<?php

class Autentifikasi extends CI_Controller
{

	public function index()
	{
		//jika statusnya sudah login, maka tidak bisa mengakses halaman login aslias dikembalikan ke tampilan user
		if($this->session->userdata('email')){
			redirect('user');
		}

		$this->form_validation->set_rules('email','Alamat Email','required|trim|valid_email', [
			'required' => 'Email Wajib Terisi'
			'valid_email' => 'Email Salah'
		]);
		$this->form_validation->set_rules('password','Password','required|trim',[
			'required' => 'Password Harus Terisi'
		]);
		if ($this->form_validation->run() == false) {
			$data['judul'] = 'Login';
			$data['user'] = '';
			// kata'login' merupakan nilai dari variabel judul dalam array $data dikirimkan ke view auto_header
			$this->load->view('templates/auto_header', $data);
			$this->load->view('autentifikasi/login');
			$this->load->view('templates/auto_footer');
		} else{
			$this->_login();
		}
	}

private function _login()
	{
		$email = htmlspecialchars($this->input->post('email',true));
		$password = $this->input->post('password', true);

		$user = $this->ModelUser->cekData(['email' => $email])->row_array();

		//jika usernya ada
		if ($user){
			// jika user sudah aktif
			if ($user['is_active'] == 1) {
				//cek password
				if (password_verifyy($password, $user['password'])) {
 					$data = [
 						'email' => $user['email'],
						 'role_id' => $user['role_id']
 					];

 					$this->session->set_userdata($data);
 					if ($user['role_id'] == 1) {
 						redirect('admin');
 					} else {
 						if ($user['image'] == 'default.jpg') {
 							$this->session->set_flashdata('pesan','<div class= "alert alert-info alert-message" role="alert">Silahkan Ubah Profile Anda untuk Ubah Photo Profil</div>');
 						}
 						redirect('user');
 					}
 				} else {
 					$this->session->set_flashdata('pesan', '<div class= "alert alert-danger alert-message" role= "alert">Password Salah</div>');
 					redirect ('autentifikasi');
 				}
 			} else {
 				$this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">User Belum Teraktifasi</div>');
 				redirect('autentifikasi');
 			}	
 		} else {
 			$this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">Email tidak terdaftar</div>');
 			redirect('autentifikasi');
 		}	
	}
}