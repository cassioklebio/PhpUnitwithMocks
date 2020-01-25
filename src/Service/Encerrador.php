<?php

namespace Alura\Leilao\Service;

use Alura\Leilao\Dao\Leilao as LeilaoDao;

class Encerrador
{
    /**
     * @var LeilaoDao
     */
    private $dao;

    /**
     * @var EnviadorEmail
     */
    private $enviadorEmail;

    /**
     * Encerrador constructor.
     * @param LeilaoDao $dao
     * @param EnviadorEmail $enviadorEmail
     */
    public function __construct(LeilaoDao $dao, EnviadorEmail $enviadorEmail)
    {
        $this->dao=$dao;

        $this->enviadorEmail = $enviadorEmail;
    }

    /**
     * Method Encerra
     */
    public function encerra()
    {
        $leiloes = $this->dao->recuperarNaoFinalizados();



        foreach ($leiloes as $leilao) {
            if ($leilao->temMaisDeUmaSemana()) {
                try{
                    $leilao->finaliza();
                    $this->dao->atualiza($leilao);
                    $this->enviadorEmail->notificarTerminoLeilao($leilao);
                }catch (\DomainException $e){
                    error_log($e->getMessage());
                }


            }
        }
    }
}
