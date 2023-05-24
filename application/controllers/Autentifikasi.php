<?php

class Autentifikasi extends CI_Controller
{
    public function index()
    {
        //jika statusnya sudah login, maka tidak bisa mengakseshalaman login alias dikembalikan ke tampilan user
        if($this->session->userdata('email')){
            redirect('user');
        }

        $this->form_validation->set_rules(
            'email', 'Alamat Email',
            'required|trim|valid_email', [
                'required' => 'Email Harus diisi!!',
                'valid_email' => 'Email Tidak Benar!!'
            ]
        );

        $this->form_validation->set_rules(
            'password', 'Password',
            'required|trim', [
                'required' => 'Password Harus diisi'
            ]
        );

        if ($this->form_validation->run() == false) {
            $data['judul'] = 'Login';
            $data['user'] = '';

            //kata 'login' merupakan nilai dari variabel judul dalam array $data dikirimkan ke view aute_header
            $this->load->view('templates/aute_header', $data);
            $this->load->view('autentifikasi/login');
            $this->load->view('templates/aute_footer');
        } else {
            $this->_login();
        }

    }

    private function _login()
    {
        $email = htmlspecialchars($this->input->post('email', true));
        $password = $this->input->post('password', true);
        $user = $this->ModelUser->cekData(['email' => $email])->row_array();

        //jika usernya ada
        if ($user) {
            //jika user sudah aktif
            if ($user['is_active'] == 1) {
                //cek password
                if (password_verify($password, $user['password'])) {
                    $data = [
                    'email' => $user['email'],
                    'role_id' => $user['role_id']
                    ];
                    $this->session->set_userdata($data);
                    if ($user['role_id'] == 1) {
                        redirect('admin');
                    } else {
                        if ($user['image'] == 'default.jpg') {
                            $this->session->set_flashdata('pesan',
                        '<div class="alert alert-info alert-message" role="alert">Silahkan
                        Ubah Profile Anda untuk Ubah Photo Profil</div>');
                        }
                        redirect('user');
                    }
                } else {
                    $this->session->set_flashdata('pesan', '<div
                    class="alert alert-danger alert-message" role="alert">Password
                    salah!!</div>');
                    redirect('autentifikasi');
                }
            } else {
                $this->session->set_flashdata('pesan', '<div
                class="alert alert-danger alert-message" role="alert">User belum
                diaktifasi!!</div>');
                redirect('autentifikasi');
            }
        } else {
            $this->session->set_flashdata('pesan', '<div
            class="alert alert-danger alert-message" role="alert">Email tidak
            terdaftar!!</div>');
            redirect('autentifikasi');
        }
    }

    public function registrasi(){
        $this->form_validation->set_rules('email', 'Email', 'is_unique[users.email]');
        $data = array(
          'nama'          => $this->input->post('nama',true),
          'password'      => md5($this->input->post('password',true)),
          'email'         => $this->input->post('email',true),
          'image'         => $this->input->post('image',true),
          'create_date'   => date('y-m-d H:i:s'),
          'role_id'       => 2,
          'is_active'     => 2,
        );
    
        if ($this->form_validation->run() === FALSE){
          echo "<script>
            alert('Username/Email ini sudah terpakai, Silahkan ganti');
            window.location='".site_url('Login#signup')."'</script>";
        }else{
          $oke = $this->M_login->tambahdata($data);
          echo "<script>
            alert('Username berhasil dibuat');
            window.location='".site_url('Login')."'</script>";
        }
    }
}