<?php
declare(strict_types=1);

namespace Scaleplan\Access;

use phpDocumentor\Reflection\DocBlock;
use Scaleplan\Access\Exceptions\AccessDeniedException;
use Scaleplan\Access\Exceptions\AccessException;
use Scaleplan\Access\Exceptions\ClassNotFoundException;
use Scaleplan\Access\Exceptions\MethodNotFoundException;
use Scaleplan\Access\Exceptions\ValidationException;
use Scaleplan\Access\Hooks\MethodAllowed;
use Scaleplan\Access\Hooks\MethodExecuted;
use Scaleplan\Access\Hooks\SanitizePassed;
use Scaleplan\HttpStatus\HttpStatusCodes;
use Scaleplan\Result\AbstractResult;
use Scaleplan\Result\DbResult;
use Scaleplan\Result\HTMLResult;
use function Scaleplan\Event\dispatch;
use function Scaleplan\Translator\translate;

/**
 * Родитель для контроллеров - проверка прав доступа, фильтрация параметров
 *
 * Class AccessControllerParent
 *
 * @package Scaleplan\Access
 */
class AccessControllerParent
{
    /**
     * @var Access
     */
    protected $access;

    /**
     * @var bool
     */
    protected $checkMethod = true;

    /**
     * @var bool
     */
    protected $validate = true;

    /**
     * AccessControllerParent constructor.
     *
     * @param Access $access
     */
    public function __construct(Access $access)
    {
        $this->access = $access;
    }

    /**
     * @return bool
     */
    public function isCheckMethod() : bool
    {
        return $this->checkMethod;
    }

    /**
     * @param bool $checkMethod
     */
    public function setCheckMethod(bool $checkMethod) : void
    {
        $this->checkMethod = $checkMethod;
    }

    /**
     * Проверка прав доступа и входных данных для метода
     *
     * @param string $className - имя класса
     * @param string $methodName - имя метода
     * @param array $args - аргументы выполнения
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws ClassNotFoundException
     * @throws Exceptions\AccessException
     * @throws Exceptions\AuthException
     * @throws Exceptions\FormatException
     * @throws MethodNotFoundException
     * @throws ValidationException
     * @throws \ReflectionException
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \Scaleplan\Event\Exceptions\ClassNotImplementsEventInterfaceException
     */
    public function checkControllerMethod(string $className, string $methodName, array $args) : array
    {
        if (!\is_array($args)) {
            throw new ValidationException(translate('access.method-accept-array', ['method' => $methodName]));
        }

        if (!class_exists($className)) {
            throw new ClassNotFoundException(translate('access.class-does-not-exist'), HttpStatusCodes::HTTP_NOT_FOUND);
        }

        $refClass = new \ReflectionClass($className);

        if (!$refClass->hasMethod($methodName)) {
            throw new MethodNotFoundException(translate('access.method-does-not-exist', ['method' => $methodName]));
        }

        $refMethod = $refClass->getMethod($methodName);
        $docBlock = new DocBlock($refMethod);

        if ($this->checkMethod
            && ($tagName = $this->access->getConfig()->get(AccessConfig::NO_CHECK_LABEL_NAME))
            && empty($docBlock->getTagsByName($tagName))
        ) {
            $this->access->checkMethodRights($refMethod, $args, $refClass);
        }

        dispatch(MethodAllowed::class);

        $accessSanitize = new AccessSanitize($this->access, $refMethod, $args);
        $validate = false;
        if ($this->validate
            && ($tagName = $this->access->getConfig()->get(AccessConfig::NO_VALIDATE_LABEL_NAME))
            && empty($docBlock->getTagsByName($tagName))
        ) {
            $validate = true;
        }

        $accessSanitize->setIsValidate($validate);
        $args = $accessSanitize->sanitizeArgs();
        dispatch(SanitizePassed::class);

        return [$refClass, $refMethod, $args,];
    }

    /**
     * @param \ReflectionMethod $method
     * @param array $args
     * @param object|null $obj
     *
     * @return mixed|DbResult|HTMLResult
     *
     * @throws AccessException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \Scaleplan\Event\Exceptions\ClassNotImplementsEventInterfaceException
     * @throws \Scaleplan\Result\Exceptions\ResultException
     */
    protected static function execute(\ReflectionMethod $method, array &$args, object $obj = null)
    {
//        $method->setAccessible(true);
        try {
            $result = $method->invokeArgs($obj, $args);
        } catch (\ReflectionException $e) {
            if (strpos($e->getMessage(), 'Trying to invoke private method') !== false) {
                throw new AccessException(translate('access.method-is-private'));
            }

            throw $e;
        }

        dispatch(MethodExecuted::class);

        if ($result instanceof AbstractResult) {
            return $result;
        }

        if (\is_array($result)) {
            return new DbResult($result);
        }

        return new HTMLResult($result);
    }

    /**
     * Проверка прав доступа и входных данных для нестатических методов
     *
     * @param string $methodName - имя метода или SQL-свойства
     * @param array $args - массив аргументов
     *
     * @return AbstractResult
     * @throws AccessDeniedException
     * @throws ClassNotFoundException
     * @throws Exceptions\AccessException
     * @throws Exceptions\AuthException
     * @throws Exceptions\FormatException
     * @throws MethodNotFoundException
     * @throws ValidationException
     * @throws \ReflectionException
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \Scaleplan\Event\Exceptions\ClassNotImplementsEventInterfaceException
     * @throws \Scaleplan\Result\Exceptions\ResultException
     */
    public function __call(string $methodName, array $args) : AbstractResult
    {
        $args = reset($args);
        [$refClass, $method, $args] = $this->checkControllerMethod(static::class, $methodName, $args);
        return static::execute($method, $args, $this);
    }
}
