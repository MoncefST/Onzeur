<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utilisateur extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->helper(array('form', 'url', 'cookie'));
        $this->load->library(array('form_validation', 'session'));
        $this->load->model('Utilisateur_model');
        $this->load->helper('html');
    }
    

    public function inscription(){
        // Définir les règles de validation
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[utilisateur.email]');
        $this->form_validation->set_rules('nom', 'Nom', 'required');
        $this->form_validation->set_rules('prenom', 'Prénom', 'required');
        $this->form_validation->set_rules('password', 'Mot de passe', 'required|min_length[8]|max_length[64]', array(
            'min_length' => 'Le {field} doit contenir au moins {param} caractères.',
            'max_length' => 'Le {field} ne doit pas dépasser {param} caractères.'
        ));
    
        if ($this->form_validation->run() == FALSE) {
            // Charger la vue avec les erreurs
            $this->load->view('layout/header_dark');
            $this->load->view('inscription');
            $this->load->view('layout/footer_dark');
        } else {
            // Récupérer les données du formulaire
            $data = array(
                'email' => $this->input->post('email'),
                'nom' => $this->input->post('nom'),
                'prenom' => $this->input->post('prenom'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT) // Hasher le mot de passe
            );
    
            // Insérer les données dans la base de données
            if ($this->Utilisateur_model->insert_user($data)) {
                $this->session->set_flashdata('success', 'Inscription réussie. Vous pouvez maintenant vous connecter.');
                redirect('utilisateur/connexion');
            } else {
                $data['error'] = 'Une erreur est survenue. Veuillez réessayer.';
                $this->load->view('layout/header_dark');
                $this->load->view('inscription', $data);
                $this->load->view('layout/footer_dark');
            }
        }
    }
    
    public function connexion(){
        // Définir les règles de validation
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Mot de passe', 'required');
    
        if ($this->form_validation->run() == FALSE) {
            // Charger la vue avec les erreurs
            $this->load->view('layout/header_dark');
            $this->load->view('connexion');
            $this->load->view('layout/footer_dark');
        } else {
            // Récupérer les données du formulaire
            $email = $this->input->post('email');
            $password = $this->input->post('password');
    
            // Vérifier les informations d'identification dans la base de données
            $user = $this->Utilisateur_model->get_user($email);
    
            if ($user && password_verify($password, $user->password)) {
                // Connexion réussie, enregistrer l'utilisateur dans la session
                $this->session->set_userdata('user_id', $user->id);
                // Définir un cookie pour indiquer que l'utilisateur est connecté
                $cookie = array(
                    'name'   => 'user_logged_in',
                    'value'  => '1',
                    'expire' => '86500', // durée de vie du cookie (1 jour)
                    'secure' => TRUE
                );
                $this->input->set_cookie($cookie);
                redirect('utilisateur/dashboard');
            } else {
                $data['error'] = 'Email ou mot de passe incorrect.';
                $this->load->view('layout/header_dark');
                $this->load->view('connexion', $data);
                $this->load->view('layout/footer_dark');
            }
        }
    }

    public function deconnexion(){
        // Détruire la session de l'utilisateur
        $this->session->unset_userdata('user_id');
        $this->session->sess_destroy();
        
        // Supprimer le cookie
        delete_cookie('user_logged_in');
        
        // Rediriger vers la page de connexion
        redirect('utilisateur/connexion');
    }    
    
    public function dashboard(){
        if(!$this->session->userdata('user_id')){
            redirect('utilisateur/connexion');
        }
    
        // Fetch les informations des utilisateurs
        $user_id = $this->session->userdata('user_id');
        $data['user'] = $this->Utilisateur_model->get_user_by_id($user_id);
    
        // Charger les vues
        $this->load->view('layout/header_dark');
        $this->load->view('dashboard', $data);
        $this->load->view('layout/footer_dark');
    }
    
    public function modifier(){
        if(!$this->session->userdata('user_id')){
            redirect('utilisateur/connexion');
        }
    
        // Definition des règles
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('nom', 'Nom', 'required');
        $this->form_validation->set_rules('prenom', 'Prénom', 'required');
    
        if ($this->form_validation->run() == FALSE) {
            $this->dashboard();
        } else {
            $user_id = $this->session->userdata('user_id');
            $data = array(
                'email' => $this->input->post('email'),
                'nom' => $this->input->post('nom'),
                'prenom' => $this->input->post('prenom')
            );
    
            if ($this->Utilisateur_model->update_user($user_id, $data)) {
                $data['success'] = 'Informations mises à jour avec succès.';
            } else {
                $data['error'] = 'Une erreur est survenue. Veuillez réessayer.';
            }
    
            $data['user'] = $this->Utilisateur_model->get_user_by_id($user_id);
            $this->load->view('layout/header_dark');
            $this->load->view('dashboard', $data);
            $this->load->view('layout/footer_dark');
        }
    }
}
