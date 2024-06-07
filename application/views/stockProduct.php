<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header box-header-background with-border">
                    <h3 class="box-title ">Mise à jour de stock </h3>

                </div>
                <!-- /.box-header -->

                <!-- form start -->

                <div class="box-body">
                    <div class="row">

                        <div class="col-md-6 col-sm-12 col-xs-12 col-md-offset-2">



                            <div id="content">


                                <div>
                                    <?php
                                    // $GLOBALS['visibleStock']=true;


                                    if ($GLOBALS['visibleStock']) {
                                    ?>
                                        <form action="<?php echo base_url(); ?>Download/stockFile" method="post" enctype="multipart/form-data">


                                            <div class="input-group mb-3">
                                                <input type="file" name="excel" accept=".xlsx, .xls" class="form-control " id="inputGroupFile02">
                                                <!-- <label class="input-group-text" for="inputGroupFile02"><i class="bi bi-file-earmark-arrow-up-fill">Upload</label> -->
                                                <button onclick="submitForm(event)" name="import" class="btn btn-success"> <i class="bi bi-file-earmark-arrow-up-fill"></i>Upload File</button>
                                            </div>
                                        </form>
                                        <p class="badge text-bg-<?php if (isset($color)) {
                                                                    echo $color;
                                                                } ?> text-wrap" style="font-size: 10px;; width:auto; height:2rem"><?php if (isset($message)) {
                                                                                                                                                                                echo $message;
                                                                                                                                                                            } ?></p>
                                    <?php
                                    } else { ?>
                                        <br />
                                        <p class="badge text-white text-bg-dark text-wrap" style="font-size: 10px; width:auto; height:auto  ;font-weight: normal; margin-top:2%">Votre stock est en cours de traitement. Vous recevrez un message dès que le processus sera terminé.</p>

                                    <?php }
                                    ?>

                                    <br />
                                    <a href="<?php echo base_url(); ?>Download/stockTemplate" class="btn btn-dark btn-lg"><i class="fa-solid fa-download"></i> Template </a>
                                    <br />
                                    <br />

                                    <?php
                                    if ($GLOBALS['failStock']) {
                                    ?>
                                        <a href="<?php echo site_url('Download/failExcelStock'); ?>" class="btn btn-dark btn-lg"><i class="fa-solid fa-download"></i> Fail </a>
                                        <br />
                                    <?php
                                    }
                                    ?>



                                </div>



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>



</div>


</section>

<script>
    const fileInput = document.querySelector("form input");
    const form = document.querySelector("form");
    function submitForm(event) {
        event.preventDefault();
        console.log(fileInput.value)
        if (fileInput.value === "") return alert("Aucun fichier n'a été sélectionné")
        let extension = fileInput.value.split(".")[1]
        console.log(extension)
        if(extension !== "xls" && extension !== "xlsx") return alert("Veuillez télécharger un fichier Excel valide.")
        form.submit();
} </script>