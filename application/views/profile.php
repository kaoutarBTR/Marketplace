<section class="content">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header box-header-background with-border">
                    <div class="col-md-offset-1">
                        <h3 class="box-title">Modifier Profile</h3>
                    </div>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form role="form" id="userform" enctype="multipart/form-data" action="<?= base_url(); ?>SupplierController" method="post" onsubmit="return validatePasswords();">

                    <div class="row">
                        <div class="col-md-8 col-md-offset-1 border-right">
                            <div class="box-body">
                           
                                <div class="form-group">
                                    
                                    <label for="exampleInputFirstname">Prenom <span class="required">*</span></label>
                                    <input type="text" id="exampleInputFirstname" placeholder="Prenom" name="firstname" class="form-control" required
                                           value="<?php if (!empty($this->session->userdata('firstname'))) {
                                               echo $this->session->userdata('firstname');
                                           } ?>"
                                        >
                                        <span class="error-message" id="firstnameError" style="display: none;color:red">Le prenom ne peut contenir que des lettres.</span>

                                </div>

                                <div class="form-group">
                                    <label for="exampleInputLastname">Nom <span class="required">*</span></label>
                                    <input type="text" id="exampleInputLastname" placeholder="Nom" name="lastname" class="form-control" required
                                           value="<?php if (!empty($this->session->userdata('lastname'))) {
                                               echo $this->session->userdata('lastname');
                                           } ?>"
                                        >
                                        <span class="error-message" id="lastnameError" style="display: none;color:red">Le nom ne peut contenir que des lettres.</span>

                                </div>                                
                                <div id="password_div">
                                    
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Nouveau Mot de passe</label>
                                        <input type="password" placeholder="Mot de passe" id="newPassword"
                                               name="newPassword"
                                               class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Confirmer Mot de passe</label>
                                        <input type="password" placeholder="Mot de passe" id="confirmPassword"
                                               name="confirmPassword"
                                               class="form-control">
                                    </div>
                                    <span class="error-message" id="passwordError" style="display: none;color:red">Les mots de passe ne correspondent pas.</span>

                                    
                                </div>
                                <p class="text-<?php if (isset($color)) { echo $color;} ?> " 
                                style="font-size: 12px;; width:auto; height:auto ;font-weight: normal; margin:1%">
                                <?php if (isset($message)) {print_r($message);} ?></p><br />
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.row end -->

                    <input type="hidden" name="id_employee" id="id_employee" value="<?= $this->session->userdata('id_employee') ?>">
                    <input type="hidden"  name="email"value="<?php if (!empty($this->session->userdata('email'))) {
                                               echo $this->session->userdata('email');
                                           } ?>" >
                    <div class="box-footer ">
                        <button type="submit" id="sbtn" class="btn btn-outline-success col-md-offset-1">
                            Envoyer
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
            <!-- /.box -->
        </div>
</section>
</div>


</section>
<script>
        document.getElementById('exampleInputFirstname').addEventListener('input', function() {
            var input = this.value;
            var errorMessage = document.getElementById('firstnameError');
            var regex = /^[a-zA-ZÀ-ÿ\s]*$/;

            if (!regex.test(input)) {
                errorMessage.style.display = 'inline';
            } else {
                errorMessage.style.display = 'none';
            }
        });
        document.getElementById('exampleInputLastname').addEventListener('input', function() {
            var input = this.value;
            var errorMessage = document.getElementById('lastnameError');
            var regex = /^[a-zA-ZÀ-ÿ\s]*$/;

            if (!regex.test(input)) {
                errorMessage.style.display = 'inline';
            } else {
                errorMessage.style.display = 'none';
            }
        });
    </script>

<script>
        function validatePasswords() {
            var newPassword = document.getElementById('newPassword').value;
            var confirmPassword = document.getElementById('confirmPassword').value;
            var errorMessage = document.getElementById('passwordError');

            if (newPassword !== confirmPassword) {
                errorMessage.style.display = 'inline';
                return false; // Prevent form submission
            }
            errorMessage.style.display = 'none';
            return true; // Allow form submission
        }
    </script>