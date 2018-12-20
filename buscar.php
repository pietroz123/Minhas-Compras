<?php 
    include("cabecalho.php");
    include("conexao.php");
    include("funcoes.php");
?>

<form action="resultado-busca.php">
    <div class="card">
        <div class="card-header">
            Buscar compras
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Palavra/frase chave</li>
                <li class="list-group-item"><input class="form-control" type="text" name="texto"></li>
                <li class="list-group-item">Data</li>
                <li class="list-group-item">De: <input class="form-control" type="date" name="dataInicio"><br>Até: <input class="form-control" type="date" name="dataFim"></li>
                <li class="list-group-item">Comprador</li>
                <li class="list-group-item">
                    <select class="custom-select" name="comprador" id="comprador">
                        <option class="text-muted">Selecione uma Opção</option>
                        <option value="0">Todos</option>
                        <?php 
                            $compradores = listar($conexao, "SELECT * FROM compradores");
                            foreach ($compradores as $comprador) :
                        ?>
                                <option value="<?= $comprador['ID']; ?>"><?= $comprador['Nome']; ?></option>
                        <?php
                            endforeach;
                        ?>
                    </select>
                </li>

            </ul>
                
            <button class="btn btn-success btn-block" type="submit">Buscar</button>
        </div>
    </div><br><br>
</form>


<?php include("rodape.php"); ?>