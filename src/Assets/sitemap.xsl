<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:sm="http://www.sitemaps.org/schemas/sitemap/0.9"
  xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
  xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"
  xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
  exclude-result-prefixes="sm image video news">

  <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>

  <xsl:template match="/">
    <html lang="en">
      <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title>Sitemap</title>
        <link rel="stylesheet" href="/sitemap.css"/>
      </head>
      <body>
        <header>
          <h1>Sitemap</h1>
          <p><xsl:value-of select="//sm:loc[1]"/></p>
        </header>
        <div class="container">
          <xsl:apply-templates/>
        </div>
      </body>
    </html>
  </xsl:template>

  <!-- Sitemap Index -->
  <xsl:template match="sm:sitemapindex">
    <p class="count"><xsl:value-of select="count(sm:sitemap)"/> sitemap files</p>
    <table>
      <thead>
        <tr>
          <th>URL</th>
          <th>Last Modified</th>
        </tr>
      </thead>
      <tbody>
        <xsl:apply-templates select="sm:sitemap"/>
      </tbody>
    </table>
  </xsl:template>

  <xsl:template match="sm:sitemap">
    <tr>
      <td><a href="{sm:loc}"><xsl:value-of select="sm:loc"/></a></td>
      <td class="meta">
        <xsl:choose>
          <xsl:when test="sm:lastmod"><xsl:value-of select="substring(sm:lastmod,1,10)"/></xsl:when>
          <xsl:otherwise>—</xsl:otherwise>
        </xsl:choose>
      </td>
    </tr>
  </xsl:template>

  <!-- URL Set -->
  <xsl:template match="sm:urlset">
    <p class="count"><xsl:value-of select="count(sm:url)"/> URLs</p>
    <table>
      <thead>
        <tr>
          <th>URL</th>
          <th>Priority</th>
          <th>Change Frequency</th>
          <th>Last Modified</th>
        </tr>
      </thead>
      <tbody>
        <xsl:apply-templates select="sm:url"/>
      </tbody>
    </table>
  </xsl:template>

  <xsl:template match="sm:url">
    <tr>
      <td><a href="{sm:loc}"><xsl:value-of select="sm:loc"/></a></td>
      <td>
        <xsl:choose>
          <xsl:when test="sm:priority">
            <span class="badge"><xsl:value-of select="sm:priority"/></span>
          </xsl:when>
          <xsl:otherwise>—</xsl:otherwise>
        </xsl:choose>
      </td>
      <td class="meta">
        <xsl:choose>
          <xsl:when test="sm:changefreq"><xsl:value-of select="sm:changefreq"/></xsl:when>
          <xsl:otherwise>—</xsl:otherwise>
        </xsl:choose>
      </td>
      <td class="meta">
        <xsl:choose>
          <xsl:when test="sm:lastmod"><xsl:value-of select="substring(sm:lastmod,1,10)"/></xsl:when>
          <xsl:otherwise>—</xsl:otherwise>
        </xsl:choose>
      </td>
    </tr>
  </xsl:template>

</xsl:stylesheet>
