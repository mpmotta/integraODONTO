<?php
require_once 'core/Controller.php';

class DashboardController extends Controller {
    public function index() {
        $this->checkAuth();
        $this->view('dashboard');
    }
}
?>
