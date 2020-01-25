<?php


namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use PHPUnit\Framework\TestCase;

///**
// * Class LeilaoDaoMock
// * @package Alura\Leilao\Tests\Service Mock
// */
//class LeilaoDaoMock extends LeilaoDao
//{
//    /**
//     * @var array $leiloes que reciberá os leilões
//     */
//    private $leiloes = [];
//
//    /**
//     * Metodo Mock para salvar
//     * @param Leilao $leilao
//     */
//  public function salva(Leilao $leilao): void
//  {
//      $this->leiloes[] = $leilao;
//  }
//
//  /**
//   * Metodo Mock recuperarNaoFinalizados
//   * @return array
//   */
//  public function recuperarNaoFinalizados(): array
//  {
//      return array_filter($this->leiloes, function (Leilao $leilao){
//          return !$leilao->estaFinalizado();
//      });
//  }
//
//    /**
//     * Metodo Mock recuperarFinalizados
//     *
//     * @return array
//     */
//  public function recuperarFinalizados(): array
//  {
//      return array_filter($this->leiloes, function (Leilao $leilao){
//          return $leilao->estaFinalizado();
//      });
//  }
//
//    /**
//     * Metodo Mock atualiza
//     * @param Leilao $leilao
//     */
//  public function atualiza(Leilao $leilao)
//  {
//
//  }
//}

/**
 * Class EncerradorTest
 * @package Alura\Leilao\Tests\Service
 */
class EncerradorTest extends TestCase
{
    /**
     * Metodo para testar Leiloes com mais de uma semana devem ser encerrados
     *
     */
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



        $leilaoDao = $this->createMock(LeilaoDao::class);
//        $leilaoDao = $this->getMockBuilder(LeilaoDao::class)
//            ->setConstructorArgs([new \PDO('sqlite::memory:')])
//            ->getMock();

        $leilaoDao->method('recuperarNaoFinalizados')
                  ->willReturn([$fiat147, $variant]);
        $leilaoDao->method('recuperarFinalizados')
            ->willReturn([$fiat147, $variant]);
        $leilaoDao->expects($this->exactly(2))
            ->method('atualiza')
            ->withConsecutive(
                [$fiat147],
                [$variant]
            );


        $encerrador = new Encerrador($leilaoDao);
        $encerrador->encerra();

        //Assert

        $leiloes = [$fiat147,$variant];
        self::assertCount(2, $leiloes);
        self::assertTrue($leiloes[0]->estaFinalizado());
        self::assertTrue($leiloes[1]->estaFinalizado());



    }
}