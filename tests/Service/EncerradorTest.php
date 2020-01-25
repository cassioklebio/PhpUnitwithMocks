<?php


namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use Alura\Leilao\Service\EnviadorEmail;
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
     * @var Encerrador
     */
    private $encerrador;


    /**
     * @var EnviadorEmail|\PHPUnit\Framework\MockObject\MockObject
     */
    private $enviadorEmail;

    /**
     * @var $leilaoFiat147 array
     */
    private $leilaoFiat147;

    /**
     * @var Leilao
     */
    private $leilaoVariant;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->leilaoFiat147 = new Leilao(
            'Fiat 147 0km',
            new \DateTimeImmutable('8 days ago')
        );

        $this->leilaoVariant = new Leilao(
            'Variant 1972 0km',
            new \DateTimeImmutable('10 days ago')
        );



        $leilaoDao = $this->createMock(LeilaoDao::class);
//        $leilaoDao = $this->getMockBuilder(LeilaoDao::class)
//            ->setConstructorArgs([new \PDO('sqlite::memory:')])
//            ->getMock();

        $leilaoDao->method('recuperarNaoFinalizados')
            ->willReturn([$this->leilaoFiat147, $this->leilaoVariant]);
        $leilaoDao->method('recuperarFinalizados')
            ->willReturn([$this->leilaoFiat147, $this->leilaoVariant]);
        $leilaoDao->expects($this->exactly(2))
            ->method('atualiza')
            ->withConsecutive(
                [$this->leilaoFiat147],
                [$this->leilaoVariant]
            );

        $this->enviadorEmail = $this->createMock(EnviadorEmail::class);

        $this->encerrador = new Encerrador($leilaoDao, $this->enviadorEmail );

    }

    /**
     * Metodo para testar Leiloes com mais de uma semana devem ser encerrados
     *
     */
    public function testLeiLoesComMaisDeUmaSemanaDeVemSerEncerrador()
    {
               $this->encerrador->encerra();

        //Assert

        $leiloes = [$this->leilaoFiat147,$this->leilaoVariant];
        self::assertCount(2, $leiloes);
        self::assertTrue($leiloes[0]->estaFinalizado());
        self::assertTrue($leiloes[1]->estaFinalizado());
    }

    public function testDeveContinuarOProcessamentoAoEncontrarErroAoEnviarEmail()
    {
        $e = new \DomainException('\'Erro ao enviar e-mail');
        $this->enviadorEmail->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->willThrowException($e);
        $this->encerrador->encerra();
    }
}