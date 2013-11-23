<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes"/>
  <xsl:variable name="threshold">0.75</xsl:variable>

  <xsl:template match="/">
    <testsuite name="Code coverage"
               tests="{count(//file)}"
               errors="{count(//file/metrics[(@coveredstatements div @statements) &lt; $threshold])}">
      <xsl:apply-templates select="//file"/>
    </testsuite>
  </xsl:template>

  <xsl:template match="file">
    <testcase name="Coverage: {@name}" assertions="1">
      <xsl:if test="(metrics/@coveredstatements div metrics/@statements) &lt; $threshold">
        <error message="Statement coverage is below {$threshold}"
               type="coverage"/>
      </xsl:if>
    </testcase>
  </xsl:template>
</xsl:stylesheet>
