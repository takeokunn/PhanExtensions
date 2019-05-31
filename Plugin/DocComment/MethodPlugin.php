<?php

namespace Drenso\PhanExtensions\Plugin\DocComment;

require_once __DIR__ . '/../../Helper/NamespaceChecker.php';

use Drenso\PhanExtensions\Helper\NamespaceChecker;
use Phan\PluginV2;
use Phan\PluginV2\PluginAwarePostAnalysisVisitor;
use Phan\PluginV2\PostAnalyzeNodeCapability;
use ast\Node;

/**
 * Class MethodPlugin
 *
 * @suppress PhanDeprecatedInterface
 */
class MethodPlugin extends PluginV2 implements PostAnalyzeNodeCapability
{
  public static function getPostAnalyzeNodeVisitorClassName(): string
  {
    return MethodVisitor::class;
  }
}

/**
 * Class MethodVisitor
 *
 * @suppress PhanUnreferencedClass, PhanDeprecatedInterface
 */
class MethodVisitor extends PluginAwarePostAnalysisVisitor
{

  /**
   * Visit method
   *
   * @param Node $node
   *
   * @throws \AssertionError
   */
  public function visitClass(Node $node)
  {
    // Retrieve the doc block
    $docComment = $node->children['docComment'];

    // Ignore empty doc blocks
    if ($docComment === NULL || strlen($docComment) == 0) {
      return;
    }

    // Retrieve all method annotations from the doc comment
    preg_match_all('/\s*\*\s*\@method\s([A-Z]\w*)/', $docComment, $matches);
    foreach ($matches[1] as $annotation) {
      NamespaceChecker::checkVisitor($this, $this->code_base, $this->context, $annotation, 'MethodStatementNotImported',
          'The classlike/namespace {CLASS} in the "method" statement was never imported (generated by Method plugin)');
    }
  }

}

// Every plugin needs to return an instance of itself at the
// end of the file in which its defined.
return new MethodPlugin(); // @phan-suppress-current-line PhanDeprecatedInterface
