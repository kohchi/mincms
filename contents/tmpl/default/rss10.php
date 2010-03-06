<?xml version="1.0" encoding="UTF-8"?>
<rdf:RDF xmlns="http://purl.org/rss/1.0/"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xml:lang="ja">

	<channel rdf:about="サイトのRSSのURL">
		<title>サイトのタイトル</title>
		<link>サイトのURL</link>
		<description>サイトの内容</description>
		<dc:date>RSSの最終更新日時2010-02-03T15:00:00+09:00</dc:date>
		<dc:language>ja</dc:language> 
		<items>
		<rdf:Seq>
<?php foreach (pick_up_at_site(4) as $a) : ?>
                <rdf:li rdf:resource="<?php theArticlePath($a['sid'], $a['id']); ?>" />
<?php endforeach; ?>
		</rdf:Seq>
		</items>
	</channel>

<?php foreach (pick_up_at_site(4) as $a) : ?>
        <item rdf:about="<?php theArticlePath($a['sid'], $a['id']); ?>">
		<title><?php print $a['title']; ?></title>
                <link><?php theArticlePath($a['sid'], $a['id']); ?></link>
		<description><![CDATA[<?php print $a['description']; ?>]]></description>
		<dc:creator>記事1の作者名</dc:creator>
		<dc:date><?php print iso8601($a['utime']); ?></dc:date>
	</item>
<?php endforeach; ?>
</rdf:RDF>
