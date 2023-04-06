<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Stmt\{
    ClassConst,
    ClassMethod,
    Property,
};

class PhpAccessModifier
{
    private bool $public = false;
    private bool $protected = false;
    private bool $private = false;
    private bool $abstract = false;
    private bool $final = false;
    private bool $static = false;

    public function __construct(ClassConst|Property|ClassMethod $stmt)
    {
        $this->public = $stmt->isPublic();
        $this->protected = $stmt->isProtected();
        $this->private = $stmt->isPrivate();
        $this->static = $stmt->isStatic();
        if ($stmt instanceof ClassMethod) {
            $this->abstract = $stmt->isAbstract();
        }
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function isProtected(): bool
    {
        return $this->protected;
    }

    public function isPrivate(): bool
    {
        return $this->private;
    }

    public function isAbstract(): bool
    {
        return $this->abstract;
    }

    public function isFinal(): bool
    {
        return $this->final;
    }

    public function isStatic(): bool
    {
        return $this->static;
    }
}
