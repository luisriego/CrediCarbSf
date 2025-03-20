<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Service\ShoppingCart;

use App\Application\UseCase\ShoppingCart\WorkflowService;
use App\Domain\Model\Discount;
use App\Domain\Model\ShoppingCart;
use App\Domain\Services\TaxCalculator;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Workflow;

class WorkflowServiceTest extends TestCase
{
    private WorkflowService $workflowService;
    private MockObject $workflowRegistry;
    private MockObject $workflow;
    private MockObject $taxCalculator;
    private MockObject $shoppingCart;
    private MockObject $discount;
    
    protected function setUp(): void
    {
        $this->workflow = $this->createMock(Workflow::class);
        
        $this->workflowRegistry = $this->createMock(Registry::class);
        $this->workflowRegistry->method('get')
            ->willReturn($this->workflow);
            
        $this->taxCalculator = $this->createMock(TaxCalculator::class);
        
        $this->shoppingCart = $this->createMock(ShoppingCart::class);
        
        $this->discount = $this->createMock(Discount::class);
        
        $this->workflowService = new WorkflowService(
            $this->workflowRegistry,
            0.16,
            $this->taxCalculator
        );
    }
    
    /**
     * @test
     */
    public function canCheckoutReturnsTrueWhenWorkflowAllowsAndCartHasItems(): void
    {
        // Prepare
        $items = new ArrayCollection(['dummy-item']);
        $this->shoppingCart->method('getItems')->willReturn($items);
        $this->workflow->method('can')
            ->with($this->shoppingCart, 'checkout')
            ->willReturn(true);
            
        // Execute
        $result = $this->workflowService->canCheckout($this->shoppingCart);
        
        // Assert
        $this->assertTrue($result);
    }
    
    /**
     * @test
     */
    public function canCheckoutReturnsFalseWhenWorkflowDoesNotAllow(): void
    {
        // Prepare
        $items = new ArrayCollection(['dummy-item']);
        $this->shoppingCart->method('getItems')->willReturn($items);
        $this->workflow->method('can')
            ->with($this->shoppingCart, 'checkout')
            ->willReturn(false);
            
        // Execute
        $result = $this->workflowService->canCheckout($this->shoppingCart);
        
        // Assert
        $this->assertFalse($result);
    }
    
    /**
     * @test
     */
    public function canCheckoutReturnsFalseWhenCartIsEmpty(): void
    {
        // Prepare
        $items = new ArrayCollection([]);
        $this->shoppingCart->method('getItems')->willReturn($items);
        $this->workflow->method('can')
            ->with($this->shoppingCart, 'checkout')
            ->willReturn(true);
            
        // Execute
        $result = $this->workflowService->canCheckout($this->shoppingCart);
        
        // Assert
        $this->assertFalse($result);
    }
    
    /**
     * @test
     */
    public function checkoutExecutesAllRequiredSteps(): void
    {
        // Prepare
        $this->shoppingCart->expects($this->once())
            ->method('checkout')
            ->with($this->discount);
            
        $this->shoppingCart->expects($this->once())
            ->method('calculateTaxWithCalculator')
            ->with($this->taxCalculator);
            
        $this->workflow->expects($this->once())
            ->method('apply')
            ->with($this->shoppingCart, 'checkout');
            
        // Execute
        $this->workflowService->checkout($this->shoppingCart, $this->discount, $this->taxCalculator);
    }
    
    /**
     * @test
     */
    public function cancellationExecutesAllRequiredSteps(): void
    {
        // Prepare
        $this->workflow->method('can')
            ->with($this->shoppingCart, 'cancel')
            ->willReturn(true);
            
        $this->shoppingCart->expects($this->once())
            ->method('cancel');
            
        $this->workflow->expects($this->once())
            ->method('apply')
            ->with($this->shoppingCart, 'cancel');
            
        // Execute
        $this->workflowService->cancel($this->shoppingCart);
    }
    
    /**
     * @test
     */
    public function canCancelReturnsTrueWhenWorkflowAllows(): void
    {
        // Prepare
        $this->workflow->method('can')
            ->with($this->shoppingCart, 'cancel')
            ->willReturn(true);
            
        // Execute
        $result = $this->workflowService->canCancel($this->shoppingCart);
        
        // Assert
        $this->assertTrue($result);
    }
    
    /**
     * @test
     */
    public function canCancelReturnsFalseWhenWorkflowDoesNotAllow(): void
    {
        // Prepare
        $this->workflow->method('can')
            ->with($this->shoppingCart, 'cancel')
            ->willReturn(false);
            
        // Execute
        $result = $this->workflowService->canCancel($this->shoppingCart);
        
        // Assert
        $this->assertFalse($result);
    }
    
    /**
     * @test
     */
    public function cancelThrowsExceptionWhenWorkflowDoesNotAllow(): void
    {
        // Prepare
        $this->workflow->method('can')
            ->with($this->shoppingCart, 'cancel')
            ->willReturn(false);
            
        // Assert & Execute
        $this->expectException(\LogicException::class);
        $this->workflowService->cancel($this->shoppingCart);
    }
    
    /**
     * @test
     */
    public function getAvailableTransitionsReturnsCorrectTransitions(): void
    {
        // Prepare
        $transition1 = $this->createMock(\Symfony\Component\Workflow\Transition::class);
        $transition1->method('getName')->willReturn('checkout');
        
        $transition2 = $this->createMock(\Symfony\Component\Workflow\Transition::class);
        $transition2->method('getName')->willReturn('cancel');
        
        $this->workflow->method('getEnabledTransitions')
            ->with($this->shoppingCart)
            ->willReturn([$transition1, $transition2]);
            
        // Execute
        $result = $this->workflowService->getAvailableTransitions($this->shoppingCart);
        
        // Assert
        $this->assertEquals(['checkout', 'cancel'], $result);
    }
}