<?php declare(strict_types=1);

namespace PHPStan\Type\Magento\MageCoreModelLayout;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;

class HelperDynamicReturnTypeExtension implements \PHPStan\Type\DynamicMethodReturnTypeExtension
{
    public static function getClass(): string
    {
        return \Mage_Core_Model_Layout::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'helper';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type
    {
        if(empty($methodCall->args[0]->value->value)) {
            return new ObjectType(\Mage_Core_Helper_Abstract::class);
        }

        $config = $this->getMagentoConfig();

        $helperName = $methodCall->args[0]->value->value;
        $helperClassName = $config->getHelperClassName($helperName);

        return new ObjectType($helperClassName);
    }

    protected function getMagentoConfig(): \Mage_Core_Model_Config
    {
        return \Mage::app()->getConfig();
    }
}