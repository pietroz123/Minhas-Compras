<?php 
    include $_SERVER['DOCUMENT_ROOT'].'/cabecalho.php'; 
    include $_SERVER['DOCUMENT_ROOT'].'/includes/funcoes.php';
    include $_SERVER['DOCUMENT_ROOT'].'/includes/funcoes-grupos.php';
?>

<?php
    verifica_usuario();

    // Recupera todos os IDs dos compradores em todos os grupos do usuário
    $ids_compradores = recupera_ids_compradores_grupos($conexao, $_SESSION['login-username'], $_SESSION['login-email']);
?>

        <h1>Formulário de Adição de Compra</h1>

        <form action="adiciona-compra.php" method="post" enctype="multipart/form-data">        
            
            <div class="grid">

                <!-- Colunas no grid sempre somam até 12 -->
                <!-- Existem 5 tipos de tamanho: xs sm md lg xl -->
            
                <div class="row">
                    <div class="col-lg-4">Observações</div>
                    <div class="col-lg-8"><input class="form-control" type="text" name="observacoes"></div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-lg-4">Data</div>
                    <div class="col-lg-8"><input class="form-control" type="date" name="data"></div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-lg-4">Valor</div>
                    <div class="col-lg-8"><input class="form-control" type="number" name="valor" min="0" step="0.01"></div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-lg-4">Desconto</div>
                    <div class="col-lg-8"><input class="form-control" type="number" name="desconto" min="0" step="0.01" value="0"></div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-lg-4">Forma de Pagamento</div>
                    <div class="col-lg-8">
                        <input type="radio" name="forma-pagamento" value="cartao" checked> Cartão<br>
                        <input type="radio" name="forma-pagamento" value="boleto"> Boleto<br>
                        <input type="radio" name="forma-pagamento" value="dinheiro"> Dinheiro<br>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-lg-4">Comprador</div>
                    <div class="col-lg-8">
                        <select class="custom-select" name="comprador-id" id="comprador-id">
                            <option class="text-muted">Selecione uma Opção</option>
                            <?php
                                foreach ($ids_compradores as $ids_comprador) {
                                    $comprador = buscar_comprador($conexao, $ids_comprador['Comprador_ID']);
                            ?>
                                    <option value="<?= $comprador['ID']; ?>"><?= $comprador['Nome']; ?></option>

                            <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <hr>
                <!-- <div class="row">
                    <div class="col-lg-4">Imagem (Opcional)</div>
                    <div class="col-lg-8">
                        <input class="form-control-file" type="file" id="input-imagem" name="imagem" data-multiple-caption="{numero} arquivos selecionados" multiple>
                        <label for="input-imagem">
                            <i class="far fa-file-image"></i>
                            <span>Selecione uma imagem</span>
                        </label>
                    </div>
                </div> -->



                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#modal-upload-imagem">faça o upload da nota fiscal</button>
                <div class="modal fade" id="modal-upload-imagem">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Faça upload da Nota Fiscal</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <input class="form-control-file" type="file" id="input-imagem" name="imagem" data-multiple-caption="{numero} arquivos selecionados" multiple>
                                <label for="input-imagem">
                                    <i class="far fa-file-image"></i>
                                    <span>Selecione uma imagem</span>
                                </label>
                                <div id="canvas">
                                    <img id="imagem-nota" alt="Imagem nota">
                                </div>
                            </div>
                            <div class="modal-footer">
                                
                            </div>
                        </div>
                    </div>
                </div>



                <hr>
            </div>
            
            <div><button type="submit" name="submit-form-compra" class="btn btn-primary btn-block align-content-center">Adicionar</button></div>

        </form>

<?php include $_SERVER['DOCUMENT_ROOT'].'/rodape.php'; ?>

<script>

    // // https://tympanus.net/codrops/2015/09/15/styling-customizing-file-inputs-smart-way/
    // var input = $('#input-imagem');
    // Array.prototype.forEach.call( input, function( input )
    // {
    //     var label	 = input.nextElementSibling,
    //         labelVal = label.innerHTML;

    //     input.addEventListener( 'change', function( e )
    //     {
    //         var fileName = '';
    //         if( this.files && this.files.length > 1 && this.files[0].type.match(/^image\//))
    //             fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{numero}', this.files.length );
    //         else
    //             fileName = e.target.value.split( '\\' ).pop();

    //         if( fileName )
    //             label.querySelector( 'span' ).innerHTML = fileName;
    //         else
    //             label.innerHTML = labelVal;
    //     });
    // });


    // =======================================================
    // Para uso do Cropper.js
    // =======================================================

    var canvas = $('#canvas');

    $('#input-imagem').on( 'change', function(e){
        if (this.files && this.files[0]) {
            var arquivos = this.files;
            var arquivo = arquivos[0];
            if ( arquivo.type.match(/^image\//) ) {

                var imagem = document.getElementById("imagem-nota");
                $('#imagem-nota').attr("src", window.URL.createObjectURL(arquivo));

                // Cria o Cropper
                var cropper = new Cropper(imagem, {
                    aspectRatio: NaN
                });

                // Adiciona o botao para cortar
                $('#modal-upload-imagem .modal-footer').append('<input type="button" class="btn btn-light btn-cortar" value="Cortar">');

                var label	 = $('#input-imagem').siblings('label');
                var labelVal = label.val();
                

                // Adiciona o nome do arquivo ao label do input
                var fileName = '';
                if( this.files && this.files.length > 1 )
                    fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{numero}', this.files.length );
                else
                    fileName = e.target.value.split( '\\' ).pop();

                if( fileName )
                    label.children('span').text(fileName);
                else
                    label.innerHTML = labelVal;


                // Coloca a imagem cortada no canvas
                $('.btn-cortar').click(function() {
                    // Get a string base 64 data url
                    var croppedImageDataURL = cropper.getCroppedCanvas().toDataURL("image/png");
                    $('#canvas').html('<img src="'+croppedImageDataURL+'" alt="Imagem cortada">');
                    arquivo = croppedImageDataURL;
                });


            }
            else {
                alert("Invalid file type! Please select an image file.");
            }
        }
        else {
            alert('No file(s) selected.');
        }
    });
    


</script>