<?php

abstract class Sisyphus_C14n_C14nAbstract_TestCase
    extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider fixtures
     */
    public function testC14N($rawContent,
        $canonicalizedContent,
        $comments,
        $exclusive,
        $subset,
        $predefinedNamespaces,
        $inclusiveNamespaces,
        $pathToRoot
    )
    {
        $rawDocument = new DOMDocument();

        $oldDirectory = getcwd();
        chdir($this->getFixturePath());
        $rawDocument->loadXml(
            $rawContent,
            LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_NOENT
        );
        chdir($oldDirectory);

        $canonicalizer = $this->getCanonicalizer()
                              ->withComments($comments)
                              ->exclusive($exclusive)
                              ->query($subset)
                              ->namespaces($predefinedNamespaces)
                              ->inclusiveNamespaces($inclusiveNamespaces);

        if ($pathToRoot) {
            $xpath = new DOMXpath($rawDocument);
            foreach ($predefinedNamespaces as $prefix => $ns) {
                $xpath->registerNamespace($prefix, $ns);
            };
            $nodeset = $xpath->query($pathToRoot);
            $rootNode = $nodeset->item(0);
        } else {
            $rootNode = $rawDocument;
        };

        $this->assertEquals(
            $canonicalizedContent,
            $canonicalizer->canonicalize($rootNode)
        );
    }

    /*
     * Data providers
     */

    public function fixtures()
    {
        return array(
            // Propagages attributes in 'xml' namespace
            array(
                <<<EOF
<e1 xml:space="preserve" xml:lang="en">
  <e2>
    <e3 xml:lang="fr" xml:space="default">
    </e3>
  </e2>
</e1>
EOF
                ,
                <<<EOF
<e3 xml:lang="fr" xml:space="preserve"></e3>
EOF
                ,
                false,
                false,
                '//e3|//e3/@xml:lang',
                array(),
                array(),
                null
            ),

            // Correctly applies subset to namespace axis
            array(
                <<<EOF
<e1 xmlns:ns1="http://a.example.com" xmlns:ns2="http://b.example.com"/>
EOF
                ,
                <<<EOF
 xmlns:ns2="http://b.example.com"
EOF
                ,
                false,
                false,
                '//namespace::ns2',
                array(),
                array(),
                null
            ),

            // Does  not  render  namespace  which parent  is  not  in
            // document subset in exlusive mode
            array(
                <<<EOF
<e1 xmlns:ns1="http://a.example.com" xmlns:ns2="http://b.example.com"/>
EOF
                ,
                <<<EOF
EOF
                ,
                false,
                true,
                '//namespace::ns2',
                array(),
                array(),
                null
            ),

            // Does not  render default namespace with  empty value if
            // parent node is not in doc subset
            array(
                <<<EOF
<e1 xmlns="" xmlns:ns2="http://b.example.com"/>
EOF
                ,
                <<<EOF
 xmlns:ns2="http://b.example.com"
EOF
                ,
                false,
                false,
                '//namespace::*',
                array(),
                array(),
                null
            ),

            // Does not  render default namespace with  empty value if
            // there no  visible ancestor with default  namespace with
            // non-empty URL.
            array(
                <<<EOF
<e1 xmlns="" xmlns:ns2="http://b.example.com"/>
EOF
                ,
                <<<EOF
<e1 xmlns:ns2="http://b.example.com"></e1>
EOF
                ,
                false,
                false,
                '//e1|//namespace::*',
                array(),
                array(),
                null
            ),

            // Does render default namespace with empty value if there
            // is  visible   ancestor  with  default   namespace  with
            // non-empty URL.
            array(
                <<<EOF
<e0 xmlns="http://default.example.com"><e1 xmlns="" xmlns:ns2="http://b.example.com"/></e0>
EOF
                ,
                <<<EOF
<e0 xmlns="http://default.example.com"><e1 xmlns="" xmlns:ns2="http://b.example.com"></e1></e0>
EOF
                ,
                false,
                false,
                '//*|//namespace::*',
                array(),
                array(),
                null
            ),

            // When elements is not in document subset, stil processes
            // namespace axis
            array(
                <<<EOF
<e1 xmlns:ns1="http://a.example.com"><e2></e2></e1>
EOF
                ,
                <<<EOF
 xmlns:ns1="http://a.example.com"<e2 xmlns:ns1="http://a.example.com"></e2>
EOF
                ,
                false,
                false,
                '//@*|//namespace::*|//e2',
                array(),
                array(),
                null
            ),

            // When element is not in document subset, still processes
            // attribute axis
            array(
                <<<EOF
<e1 attr1="value"><e2></e2></e1>
EOF
                ,
                <<<EOF
 attr1="value"<e2></e2>
EOF
                ,
                false,
                false,
                '//@*|//namespace::*|//e2',
                array(),
                array(),
                null
            ),

            // When elements is not in document subset, still processes
            // child elements
            array(
                <<<EOF
<e1 attr1="value"><e2><e3/></e2></e1>
EOF
                ,
                <<<EOF
 attr1="value"<e3></e3>
EOF
                ,
                false,
                false,
                '//@*|//namespace::*|//e3',
                array(),
                array(),
                null
            ),

            // Skips PI nodes when they're not in the doc subset
            array(
                <<<EOF
<?test1?>
<e3/>
<?test2?>
EOF
                ,
                <<<EOF
<?test1?>
<e3></e3>
EOF
                ,
                false,
                false,
                '//e3|//processing-instruction(\'test1\')',
                array(),
                array(),
                null
            ),

            // Misc samples
            array(
                <<<EOF
<doc xmlns:n1="http://a.example.com" xmlns:n2="http://b.example.com"><e1><e2 n1:test="test"></e2></e1></doc>
EOF
                ,
                <<<EOF
<e2 xmlns:n1="http://a.example.com" n1:test="test"></e2>
EOF
                ,
                false, // no comments
                true, // exclusuve
                null,
                array(),
                array(),
                '//e2'
            ),

            // Samples from Canonical XML and Exclusive XML
            // Canonicalization standards
            array(
                <<<EOF
<doc xmlns:n1="http://a.example.com" xmlns:n2="http://b.example.com"><e1><e2 n1:test="test"></e2></e1></doc>
EOF
                ,
                <<<EOF
<e2 xmlns:n1="http://a.example.com" xmlns:n2="http://b.example.com" n1:test="test"></e2>
EOF
                ,
                false, // no comments
                false, // exclusuve
                null,
                array(),
                array(),
                '//e2'
            ),
            array(
                $this->fixture('exclusive-subset.xml'),
                $this->fixture('exclusive-subset.canonical.xml'),
                false, // no comments
                true, // exclusuve
                $this->fixture('exclusive-subset.subset.xpath'),
                array(),
                array(),
                '//e2'
            ),
            array(
                $this->fixture('exclusive-subset.xml'),
                $this->fixture('exclusive-subset.canonical2.xml'),
                false, // no comments
                true, // exclusuve
                $this->fixture('exclusive-subset.subset2.xpath'),
                array(),
                array(),
                '//e2'
            ),
            array(
                $this->fixture('exclusive-subset.xml'),
                $this->fixture('exclusive-subset.canonical3.xml'),
                false, // no comments
                true, // exclusuve
                $this->fixture('exclusive-subset.subset3.xpath'),
                array(),
                array(),
                '//e2'
            ),
            array(
                $this->fixture('exclusive-subset.xml'),
                $this->fixture('exclusive-subset.canonical4.xml'),
                false, // no comments
                true, // exclusuve
                null, // no subset
                array(),
                array(),
                '//e2'
            ),
            array(
                $this->fixture('document7.xml'),
                $this->fixture('document7.canonical.xml'),
                false,
                false,
                $this->fixture('document7.subset.xpath'),
                array(
                    'ietf' => 'http://www.ietf.org'
                ),
                array(
                ),
                null
            ),
            array(
                $this->fixture('document1.xml'),
                $this->fixture('document1.canonical.xml'),
                false,
                false,
                null,
                array(),
                array(),
                null
            ),
            array(
                $this->fixture('document1.xml'),
                $this->fixture('document1.canonical-commented.xml'),
                true,
                false,
                null,
                array(),
                array(),
                null
            ),
            array(
                $this->fixture('document2.xml'),
                $this->fixture('document2.canonical.xml'),
                false,
                false,
                null,
                array(),
                array(),
                null
            ),
            array(
                $this->fixture('document3.xml'),
                $this->fixture('document3.canonical.xml'),
                false,
                false,
                null,
                array(),
                array(),
                null
            ),
            array(
                $this->fixture('document4.xml'),
                $this->fixture('document4.canonical.xml'),
                false,
                false,
                null,
                array(),
                array(),
                null
            ),
            array(
                $this->fixture('document5.xml'),
                $this->fixture('document5.canonical.xml'),
                false,
                false,
                null,
                array(),
                array(),
                null
            ),
            array(
                $this->fixture('document6.xml'),
                $this->fixture('document6.canonical.xml'),
                false,
                false,
                null,
                array(),
                array(),
                null
            ),
            array(
                $this->fixture('document8.xml'),
                $this->fixture('document8.canonical.xml'),
                false,
                true,
                $this->fixture('document8.subset.xpath'),
                array(
                    'n0' => 'http://a.example',
                    'n1' => 'http://b.example'
                ),
                array(
                    'n0',
                    'n1'
                ),
                null
            ),

            // Practical SAML Assertion response canonicalization
            // examples
            array(
                $this->fixture('saml.xml'),
                $this->fixture('saml.canonical.xml'),
                false,
                true,
                null,
                array(),
                array(),
                null
            ),
            array(
                $this->fixture('saml2.xml'),
                $this->fixture('saml2.canonical.xml'),
                false,
                true,
                null,
                array(
                    'secdsig' => 'http://www.w3.org/2000/09/xmldsig#'
                ),
                array(),
                './secdsig:SignedInfo'
                ),
        );
    }

    /*
     * Fixture loading helpers
     */

    protected function getFixturePath()
    {
        return sprintf(
            '%s/tests/fixtures',
            realpath(dirname(__FILE__) . '/../../../..')
        );
    }

    protected function fixture($name)
    {
        return trim(file_get_contents(
            sprintf(
                '%s/%s',
                $this->getFixturePath(),
                $name
            )
        ));
    }

    abstract protected function getCanonicalizer();
}