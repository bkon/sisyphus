<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes"/>

  <xsl:template match="/">
    <testsuite name="PHP Mess Detector code quality tests"
               tests="{count(//file)}"
               errors="{count(//file)}">
      <xsl:apply-templates select="//file/violation"/>
    </testsuite>
  </xsl:template>

  <xsl:template match="violation">
    <testcase name="{../@name} {@beginline}:{@endline}">
      <error message="{text()}"
             type="{@rule}: {@ruleset}">
      </error>
    </testcase>
  </xsl:template>
</xsl:stylesheet>
