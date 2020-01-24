<?php


namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use PHPUnit\Framework\TestCase;

/**
 * Class LeilaoDaoMock
 * @package Alura\Leilao\Tests\Service Mock
 */
class LeilaoDaoMock extends LeilaoDao
{
    /**
     * @var array $leiloes que reciberá os leilões
     */
    private $leiloes = [];

    /**
     * Metodo Mock para salvar
     * @param Leilao $leilao
     */
  public function salva(Leilao $leilao): void
  {
      $this->leiloes[] = $leilao;
  }

    /**
     * Metodo Mock recuperarNaoFinalizados
     * @return array
     */
  public function recuperarNaoFinalizados(): array
  {
      return array_filter($this->leiloes, function (Leilao $leilao){
          return !$leilao->estaFinalizado();
      });
  }

  public function recuperarFinalizados(): array
  {
      return array_filter($this->leiloes, function (Leilao $leilao){
          return $leilao->estaFinalizado();
      });
  }

    /**
     * Metodo Mock atualiza
     * @param Leilao $leilao
     */
  public function atualiza(Leilao $leilao)
  {

  }
}

/**
 * Class EncerradorTest
 * @package Alura\Leilao\Tests\Service
 */
class EncerradorTest extends TestCase
{


    public function testLeiLoesComMaisDeUmaSemanaDeVemSerEncerrador()
    {
        $fiat147 = new Leilao(
            'Fiat 147 0km',
            new \DateTimeImmutable('8 days ago')
        );

        $variant = new Leilao(
            'Variant 1972 0km',
            new \DateTimeImmutable('10 days ago')
        );



        $leilaoDao = new LeilaoDaoMock();
        $leilaoDao->salva($fiat147);
        $leilaoDao->salva($variant);

        $encerrador = new Encerrador($leilaoDao);
        $encerrador->encerra();

        //Assert
        $leiloes = $leilaoDao->recuperarFinalizados();
        self::assertCount(2, $leiloes);
        self::assertEquals(
            'Fiat 147 0km',
            $leiloes[0]->recuperarDescricao());

        self::assertEquals('Variant 1972 0km', $leiloes[1]->recuperarDescricao());

    }
}