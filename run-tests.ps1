# Script para executar testes com detalhes no PowerShell
# Uso: .\run-tests.ps1 [opcoes]

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  RHease - Executando Testes PHPUnit" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

$opcao = $args[0]

if ($null -eq $opcao -or $opcao -eq "") {
    Write-Host "Executando TODOS os testes com detalhes..." -ForegroundColor Yellow
    Write-Host ""
    & vendor\bin\phpunit --testdox --colors=always
}
elseif ($opcao -eq "unit") {
    Write-Host "Executando testes UNITARIOS..." -ForegroundColor Yellow
    Write-Host ""
    & vendor\bin\phpunit --testdox --colors=always tests/Unit
}
elseif ($opcao -eq "model") {
    Write-Host "Executando testes do MODEL..." -ForegroundColor Yellow
    Write-Host ""
    & vendor\bin\phpunit --testdox --colors=always tests/Unit/Model
}
elseif ($opcao -eq "controller") {
    Write-Host "Executando testes do CONTROLLER..." -ForegroundColor Yellow
    Write-Host ""
    & vendor\bin\phpunit --testdox --colors=always tests/Unit/Controller
}
elseif ($opcao -eq "vagas") {
    Write-Host "Executando testes do modulo de VAGAS..." -ForegroundColor Yellow
    Write-Host ""
    & vendor\bin\phpunit --testdox --colors=always tests/Unit/Model/GestaoVagasModelTest.php
    & vendor\bin\phpunit --testdox --colors=always tests/Unit/Controller/GestaoVagasControllerTest.php
}
elseif ($opcao -eq "verbose") {
    Write-Host "Executando com VERBOSE (mais detalhes)..." -ForegroundColor Yellow
    Write-Host ""
    & vendor\bin\phpunit --verbose --testdox --colors=always
}
elseif ($opcao -eq "help") {
    Write-Host ""
    Write-Host "Uso: .\run-tests.ps1 [opcao]" -ForegroundColor Green
    Write-Host ""
    Write-Host "Opcoes disponiveis:" -ForegroundColor Yellow
    Write-Host "  [vazio]      - Executa todos os testes com detalhes"
    Write-Host "  unit         - Executa apenas testes unitarios"
    Write-Host "  model        - Executa apenas testes do Model"
    Write-Host "  controller   - Executa apenas testes do Controller"
    Write-Host "  vagas        - Executa apenas testes do modulo de vagas"
    Write-Host "  verbose      - Executa com mais detalhes (verbose)"
    Write-Host "  help         - Mostra esta ajuda"
    Write-Host ""
}
else {
    Write-Host "Executando teste especifico: $opcao" -ForegroundColor Yellow
    Write-Host ""
    & vendor\bin\phpunit --testdox --colors=always --filter $opcao
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Testes finalizados!" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

