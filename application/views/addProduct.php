<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header box-header-background with-border">
                    <h3 class="box-title ">Importer la nouvelle liste des produits </h3>

                </div>
                <!-- /.box-header -->

                <!-- form start -->

                <div class="box-body">
                    <div class="row">

                        <div class="col-md-6 col-sm-12 col-xs-12 col-md-offset-2">



                            <div id="content">


                                <div>
                                    <p class="badge text-bg-success text-wrap" style="font-size: 10px;; width:auto; height:auto"><?php if (isset($status)) {
                                                                                                                                        echo $status;
                                                                                                                                    } ?></p>
                                    <?php if ($GLOBALS['visible']) { ?>
                                        <form action="<?php echo base_url(); ?>Download/productFile" method="post" enctype="multipart/form-data">

                                            <div class="input-group mb-3">
                                                <input type="file" name="excel" accept=".xlsx, .xls" class="form-control " id="inputGroupFile02">
                                                <!-- <label class="input-group-text" for="inputGroupFile02"><i class="bi bi-file-earmark-arrow-up-fill">Upload</label> -->
                                                <button onclick="submitForm(event)" name="import" class="btn btn-success"> <i class="bi bi-file-earmark-arrow-up-fill"></i>Upload File</button>

                                            </div>
                                        </form>

                                    <?php  } else { ?>
                                        <br />
                                        <p class="badge text-white text-bg-dark text-wrap" style="font-size: 10px; width:auto; height:auto  ;font-weight: normal; margin-top:2%">Vos produits sont en cours de création. Vous recevrez un message dès que le processus sera terminé.</p>

                                    <?php } ?>



                                    <br />
                                    <a href="<?php echo base_url('Download/productTemplate') ?>" class="btn btn-success btn-lg"><i class="fa-solid fa-download"></i> Template </a>

                                    <br />
                                    <p class="text-<?php if (isset($color)) {
                                                        echo $color;
                                                    } ?> " style="font-size: 12px;; width:auto; height:auto ;font-weight: normal; margin:1%"><?php if (isset($message)) {
                                                                                                                                                                                    print_r($message);
                                                                                                                                                                                } ?></p>
                                    <br />
                                    <?php
                                    if ($GLOBALS['fail']) { ?>

                                        <p class="text-danger" style="font-size: 10px; width:auto; height:auto  ;font-weight: normal; margin-top:2%">Certains produits n'ont pas été créés en raison d'une erreur dans le fichier Excel. Veuillez télécharger le fichier ci-dessous pour voir la liste des produits concernés.</p>

                                        <a href="<?php echo base_url('Download/failExcel') ?>" class="btn btn-dark btn-lg"><i class="fa-solid fa-download"></i>Produits non crées</a>
                                    <?php }  ?>

                                    <?php if (isset($tableau)) { ?>

                                        <table class="table no-margin">
                                            <?php foreach ($tableau as $key => $row) : ?>
                                                <?php if ($key === 0) : // First row is for table headers 
                                                ?>
                                                    <tr class="custom-tr">
                                                        <?php foreach ($row as $cellValue) : ?>
                                                            <th><?php echo $cellValue; ?></th>
                                                        <?php endforeach; ?>
                                                    </tr>
                                                <?php else : // Subsequent rows are for data 
                                                ?>
                                                    <tr class="custom-tr">
                                                        <?php foreach ($row as $cellValue) : ?>
                                                            <td class="vertical-td"><?php echo $cellValue; ?></td>
                                                        <?php endforeach; ?>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </table>
                                    <?php } ?>
                                    <br />

                                    <p class="badge text-bg-warning text-wrap" style="font-size: 10px; font-size: 10px; width:auto; height:auto  ;font-weight: normal"><?php if (isset($messageTemplate)) {
                                                                                                                                                                            echo $messageTemplate;
                                                                                                                                                                        } ?></p>
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