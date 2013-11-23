<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes"/>

  <xsl:template match="/">
    <testsuite name="PHP_CodeSniffer code quality tests"
               tests="{count(//error)}"
               errors="{count(//error)}">
      <xsl:apply-templates select="//error"/>
    </testsuite>
  </xsl:template>

  <xsl:template match="error">
    <testcase name="{../file/@name} {@line}:{@column} {@source}">
      <error message="{text()}"
             type="{@source}">
      </error>
    </testcase>
  </xsl:template>
</xsl:stylesheet>
