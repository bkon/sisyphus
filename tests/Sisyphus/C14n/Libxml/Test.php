<?php

class Sisyphus_C14n_Libxml_Test
    extends Sisyphus_C14n_C14nAbstract_TestCase
{
    protected function getCanonicalizer()
    {
        return new Sisyphus_C14n_Libxml();
    }
}