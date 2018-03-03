# Plugin Informatie

**Plugin Naam**: GK Steemit Info<br/>
**Bijdragers**: mcfarhat<br/>
**Tags**: wordpress, steemit, widget, user count<br/>
**Minimale vereisten**: 4.3<br/>
**Getest tot**: 4.9<br/>
**Stabiele tag**: trunk<br/>
**Licensie**: GPLv2 or later<br/>
**Licensie URL**: https://www.gnu.org/licenses/gpl-2.0.html<br/>

# Korte Samenvatting

De plugin dient een innovatieve benadering te zijn voor het verbinden van Steemit met Wordpress. In de huidige fase is het mogelijk 
om een of meerdere widgets / shortcodes toe te voegen die Steemit- gerelateerde informatie kunnen weergeven, inclusief enkele Steemit- statistieken, gebruikersinformatie, berichten van gebruikers en populaire berichten, samen met een groot aantal filteropties, maar maakt het ook mogelijk om Steemit- gebruikers onmiddelijk aan te maken via het delegeren van Steem Power.

# Plugin Details

Steemit.com is een snel groeiend sociaal netwerk / blogging platform gebouwd op de Steem Blockchain, en die beloont auteurs en curators voor kwaliteitsinhoud via het concept van upvotes (likes).

Wij geloven dat het bieden van de juiste middelen om informatie uit Steemit te integreren in Wordpress een weg zal vinden voor verdere groei richting het platform, met name via widgets / shortcodes, omdat deze gemakkelijk te implementeren zijn voor iedere eigenaar van een Wordpress- site, zonder te hoeven beschikken over de nodige ontwikkelings- vaardigheden.

## Widgets/Shortcodes

Door GK Steemit Info te gebruiken, heeft u de mogelijkheid om widgets te maken of shortcodes in te sluiten in uw Wordpress oplossing, maar ook nieuwe gebruikers op Steemit aanmaken, op voorwaarde dat u Steem Power aan de nieuwe account delegeert.

### Steemit Gebruikersgegevens

Deze widget maakt het mogelijk om informatie van één of meer Steemit gebruiker(s) weer te geven via widgets / shortcodes. Bij het slepen en neerzetten van de widget "Steemit User Info", krijgt u de mogelijkheid om te selecteren van wie u de informatie wilt weergeven. 
De weergegeven informatie zal vervolgens elke 30 seconden automatisch worden vernieuwd en bevat de volgende details:
- Gebruikersnaam.
- Afbeelding gebruikersprofiel.
- Veld 'meer info'.
- Locatie.
- Website.
- Totaal aantal berichten.
- STEEM Power inclusief eigen STEEM Power (SP), gedelegeerde SP, ontvangen SP, en effectieve SP (na het toevoegen en verwijderen van onvangen en gedelegeerde SP).
- STEEM en SBD actuele waarde.
- Actuele stemkracht.
- Reputatie.
- Geschatte accountwaarde opgehaald uit Steemit (wat het gemiddelde is van de STEEM / SBD USD waarde van de afgelopen 7 dagen).
- Realtime accountwaarde berekend met de realtime STEEM en SBD USD marktwaarde zoals afgeleid uit de <a href="coinmarketcap.com">coinmarketcap.com's API</a>

Bekijk screenshots 1, 2 en 3 hieronder voor een impressie van de widget, de configuratieopties, en het proefresultaat van de widget.

Er is ook een shortcode versie beschikbaar, die op elke pagina gebruikt kan worden of in code. De code hiervoor is: [steemit_user_info username=USERNAME] waarbij username de gebruikersnaam is van het Steemit account.

### Steemit User Posts

We hebben besloten om deze nieuwe widget toe te voegen, omdat markeren van de Steemit berichten belangrijk is voor de eigenaar van de Wordpress site of elke andere Steemian die de eigenaar wil benadrukken. Dit biedt flexibiliteit om binnen een of meerdere widgets, of binnen specifieke pagina's voor Steemit berichten een groot aantal berichten weer te geven die zijn gefilterd op bepaalde auteurs, waarbij je de flexibiliteit hebt om verschillende parameters in te stellen. De widget toont een lijst met berichten, één op elke lijn, waarvan de titel wordt weergegeven met een link naar het originele Steemit bericht, inclusief het aantal stemmen (votes) die het bericht heeft ontvangen en het uitbetalingsbedrag dat is gekoppeld aan de post in STEEM (of de beloningen zijn uitbetaald óf nog uitbetaald moeten worden).

Bekijk de schermafbeeldingen 4, 5 en 6 hieronder voor een hoogtepunt van de widget, de configuratie- opties en een voorbeeld van de opgegeven instellingen verwerkt in de widget.

In detail uitgewerkt, wanneer de widget eenmaal is toegevoegd aan jouw specifieke zijbalk, heeft deze de standaard voorgeselecteerde instellingen die u kunt wijzigen om uw widget verder in te stellen. Dit werkt als volgt:
- Titel: dit refereert aan de titel / kop van jouw widget.
- Steemit Gebruikersnaam: dit refereert aan de gebruikersnaam of de auteur van het bericht op Steemit, zonder de @ te gebruiken. In het geval de ingevoerde gebruiker niet bestaat, zal er geen resultaat worden weergegeven.
- Max. aantal berichten: met deze limiet kunt u het aantal berichten dat aan de widget worden toegekend beperken. En hoe kleiner het aantal, hoe sneller het resultaat zal worden weergegeven. Dit komt door de wachtrij van de steemjs API. De standaardwaarde voor het max. aantal berichten staat ingesteld op 10, en het maximum aantal bedraagt 100. Dit om timeouts te voorkomen.
- Filteren op tag: hiermee kunt u de berichten van de geselecteerde auteur filteren op specifieke tags. Dit kan erg handig zijn, wanneer je bijvoorbeeld een referentie wilt weergeven naar jouw berichten met de tag "fotografie", terwijl je in een andere widget je berichten met de tag "crypto" of "wordpress" kan weergeven.
- Gedeelde berichten uitsluiten: Als je geen berichten wilt weergeven die je gedeeld hebt van andere gebruikers, heb je de optie om deze niet weer te geven. De standaard instelling voor gedeelde berichten staat ingeschakeld. 
- Minimum betaling: Steemit draait grotendeels om de beloningen voor berichten. Als u alleen berichten wilt weergeven die een bepaald minimumbedrag hebben opgehaald, kan je dat hier instellen. De waarde die u hier ingeeft staat gelijk aan de waarde in STEEM die aan de post wordt uitbetaald, of deze al is uitbetaald of nog uitbetaald moet worden. Houd er rekening mee dat het aanpassen van deze waarden voor de SteemJSD API meer tijd nodig hebben, voordat de berichten kunnen worden weergegeven.
Een versie met shortcode is tevens beschikbaar voor gebruik op elke pagina met content of in uw dynamische code. Deze is bereikbaar door de volgende code toe te passen: [steemit_user_posts username=USERNAME limit=LIMIT excluderesteem=1 minpay=0 filtertag=TAG]
waarbij: 
- Username (Vereist) is de Steemit gebruikersnaam, zijnde het doel.
- Limit (Optioneel) is de bovenste limiet van het aantal berichten om weer te geven.
- Excluderesteem (Optioneel) staat toe om gedeelde berichten van andere gebruikers te vermijden en zal resulteren in de weergave van alleen de originele berichten van deze auteur.
- Minpay (Optioneel) instelling waarbij u een minimum bedrag op kunt geven om alleen berichten weer te geven waarbij minimaal het ingestelde bedrag zal terugkeren als beloning voor het bericht. llows setting a minimum payment amount of the post to be returned back and displayed.
- Filtertag (Optioneel) staat toe berichten te filteren op één specifieke tag, zodat berichten zonder deze tag niet weergegeven zullen worden.

### Populaire Steemit Berichten

Het doel van deze widget is om snelle toegang te verlenen en zichtbaarheid via jouw wordpress site / installatie in de populaire berichten die momenteel op Steemit worden weergegeven.
Deze widget biedt meerdere configuratie- mogelijkheden en kan makkelijk versleept en geplaatst worden binnen het widgets gebied, maar biedt ook ondersteuning bij het gebruik van shortcode om zo de info in uw pagina te verwerken..
Configuratie- mogelijkheden:
- Maximaal aantal berichten: om het maximaal aantal weer te geven posts te kunnen bepalen. Standaarwaarde is 10, max is 100. 
- Filteren op tag: Niet ingevuld, zullen alle berichten worden weergegeven, wel ingevuld zullen alleen berichten met deze specifieke tag worden weergegeven.

Bekijk onderstaande schermafbeeldingen 7, 8 en 9 voor een hoogtepunt van de widget, de configuratie- opties, en de voorbeelduitkomst van de widget.

In termen van ondersteuning voor shortcode, kan de volgende shortcode gebruikt worden voor deze weergave: [steemit_trending_posts limit=LIMIT filtertag=TAG]
waarbij:
- limiet is een optioneel attribuut dat standaard staat ingesteld op 10.
- filteren van tags is een optioneel attribuut, waarmee weergave van alle populaire berichten kan worden beperkt. Als alternatieve instelling kan deze waarde één tag bevatten, waardoor alleen de berichten met deze specifieke tag worden weergegeven.

### Steemit Info

In zijn eerste versie, kon de plugin een widget weergeven die liet zien hoeveel gebruikers er op Steemit geregisteerd zijn. Er zijn nu al meer dan 740.000 registraties op Steemit.
De widget is nu verbeterd en geüpgradet, met een uitgebreide functionaliteit. 
Nu is het mogelijk om je widget een titel te geven, maar ook de vernieuwings- frequentie zak worden ingesteld. De standaard waarde hiervan staat ingesteld opm 5,000 ms (5 seconden), en kan worden verhoogd naar waarden tot 500 ms (een halve seconde). Deze flexibiliteit wordt jou als wordpress site eigenaar ter beschikking gesteld als computerintensieve functionaliteit op uw site. 
In zijn huidigde versie, bestaat het display van de widget uit: 
- steemit aantal gebruikers.
- SBD and STEEM huidige waarde, aangeleverd via steem js API.
- STEEM/USD en SBD/USD huidige waarde, aangeleverd via <a href="coinmarketcap.com">CoinMarketCap.com's API</a> voorzien van de 1 uurs, 24 uurs, en 7 dagen beweging (opwaarts en neergaande trend), alswel de rangorde van de valuta's op dit moment gerelateerd aan andere cryptocurrencies.

Bekijk onderstaande schermafbeeldingen 10, 11 en 12 voor een hoogtepunt van de widget, de configuratie- opties, en de voorbeelduitkomst van de widget.

De widget zal ook een link bevatten die refereert naar mensen op steemit.com als promotioneel aspect van de widget.

U kunt als alternatief gebruik maken van de shortcode versie, door de volgende code te gebruiken [steemit_user_count refresh_frequency=5000] waarbij vernieuwingsfrequentie (optioneel) instelt hoe vaak data ververst wordt. U kunt deze instelling overslaan, zodat de standaardwaarden van 5,000 ms (5 seconden) worden gehanteerd.

## Backend Management

### Create New Steemit User

De plug-in biedt ook een backend- menu, waarmee nieuwe Steemit- gebruikers direct kunnen worden aangemaakt. Na de installatie wordt een nieuw menu beschikbaar gesteld binnen de Wordpress Backend Management Interface, waarbij een linker menu- pictogram "GK Steemit" zal worden toegevoegd. (zie schermafbeelding 13 hieronder).
Door op dit menuitem te klikken opent er een nieuwe interface waar een nieuwe gebruiker kan worden aangemaakt (zie schermafbeelding 14 hieronder).
Deze interface bevat de volgende velden:
- Nieuw Account Naam: hier kan de nieuwe gebruikersnaam worden opgegeven. Voer de gewenste nasam in zonder het @ teken. 
- Nieuw Account Wachtwoord: Dit is het wachtwoord / WIF die gekoppeld wordt aan deze account. We raden aan een complex wachtwoord te creeëren, bij voorkeur met een <a href="http://passwordsgenerator.net/">http://passwordsgenerator.net/</a> en minimaal 50 karakters. Zorg ervoor dat u geen symbolen aan het wachtwoord toevoegt, omdat deze een validatiefout zullen veroorzaken tijdens het aanmaken van de account en niet ondersteund worden door Steemit API. U wordt verzocht alleen hoofdletters, kleine letters en numerieke tekens te gebruiken.
- Eigenaar Account Naam: dit identificeert uw eigen account, of welke andere account gebruikt wordt voor het aanmaken van de nieuwe account, en wiens SP gebruikt zal worden voor het delegeren. Nogmaals wijs ik u erop dat het @ teken niet voor de accountnaam geplaatst moet worden.
- Eigenaar WIF/Private Key: verwijst naar de account van de eigenaar, en wordt via API verzonden voor het aanmaken en het delegeren van het bedrag naar de nieuwe account.
- Fooi (in STEEM): dit is een vereist bedrag dat wordt verzonden tijdens het creeëren van de nieuwe account, en zal uiteindelijk in de nieuwe account belanden. Aangeraden wordt een bedrag van 0.200 STEEM. Geef alleen numerieke waarden op in dit veld.
- Delegatie (in VESTS): Dit is het bedrag aan VESTS dat zal worden gedelegeerd vanuit de eigenaar account naar de nieuwe account. De standaardwaarde bedraagt 30663.815330 VESTS en staat gelijk aan 15 SP. Dit bedrag kan je indien gewenst verhogen, echter zal verlagen van dit bedrag kunnen leiden tot fouten in het creeëren van de account.

Wanneer u op de knop creeëren klikt, wordt er eerst gecheckt of de gebruikersnaam die u heeft ingevoerd een geldig formaat heeft, niet bestaat, en of het bedrag aan fooi en delegatie juist zijn.Het meldingsscherm rechts boven de knop zal een foutmelding of een bevestiging van de handeling weergeven. (Bekijk schermafbeelding 15 onderaan om een bestaande melding van een account kunt zien).

Wees u ervan bewust dat na het aanmaken van een account en SP gedelegeerd te hebben, uw delegatie eventueel geannuleerd kan worden. Echter zal een account met minder dan 15 SP niet goed kunnen functioneren op Steemit, maar het duurt 7 dagen voordat de gedelegeerde SP naar de account van de eigenaar zal terugkeren. 

## Installatie

1. Upload de plugin bestanden naar de `/wp-content/plugins/gk-steemit-info` map, of installeer de plugin direct vanuit het Wordpress plugins scherm.
2. Activeer de plugin via het 'Plugins' scherm in Wordpresss.
3. a. Ga naar weergave -> widgets scherm, en selecteer welke widget(s) tussen degene met het label "Steemit" u toe wilt voegen aan uw visuele scherm, en configureer deze overeenkomstig. U kunt meerdere invoeren toevoegen voor elke widget met verschillende instellingen. 
3. b. Als alternatief kunt u de relevante shortcodes gebruiken die boven elke pagina gemarkeerd in uw code worden weergegeven.
4. Een nieuw menu zal aan de Wordpress backend adminstratie worden toegevoegd onder de naam "GK Steemit Info" waarmee u toegang heeft tot de Steemit gebruiker aanmaken functionaliteit.
5. Dat is het! 

## Screenshots
1. <a href="https://www.dropbox.com/s/8q2m7prow3ro13h/steemit_user_info_widget.png?dl=0">Schermafbeelding van Steemit gebruikersinformatie widget op het selectiescherm</a>
2. <a href="https://www.dropbox.com/s/dgo0619z1826nts/steemit_user_info_configuration.png?dl=0">Schermafbeelding van configuratieopties voor Steemit gebruikersinformatie widget</a>
3. <a href="https://www.dropbox.com/s/0h4bdhm5oryyaqz/steemit_user_info_sample_display.png?dl=0">schermafbeelding van een proefuitkomst van Steemit gebruikersinformatie widget</a>
4. <a href="https://www.dropbox.com/s/vngf4dt3h9zdgys/steemit_user_posts_widget.png?dl=0">schermafbeelding van nieuwe Steemit gebruikersbericht widget op het selectiescherm</a>
5. <a href="https://www.dropbox.com/s/21q53wkxfsj50ev/steemit_user_posts_configuration.png?dl=0">schermafbeelding van configuratieopties van een Steemit gebruikersbericht widget</a>
6. <a href="https://www.dropbox.com/s/96q2l09bnqe6uzi/display_steemit_user_posts.png?dl=0">schermafbeelding van een proefuitkomst van een Steemit gebruikersbericht widget met standaard configuratie- instellingen</a>
7. <a href="https://www.dropbox.com/s/qo0qtymzzf1tjma/steemit_trending_posts_widget.png?dl=0">schermafbeelding van populaire Steemit berichten widget op het selectiescherm</a>
8. <a href="https://www.dropbox.com/s/fl0l2gkbh4mfvct/steemit_trending_posts_configuration.png?dl=0">schermafbeelding met de configuratieopties van Steemit Populaire Berichten widget</a>
9. <a href="https://www.dropbox.com/s/rzbwmmif49d2e82/steemit_trending_posts_display.png?dl=0">schermafbeelding van proefuitkomst Steemit Populaire Berichten widget</a>
10. <a href="https://www.dropbox.com/s/macix3vv85gme2b/new_widget.png?dl=0">Schermafbeelding met de nieuwe "Steemit" Info widget</a>
11. <a href="https://www.dropbox.com/s/stgttgdgrvpncx1/widget_options.png?dl=0">Schermafbeelding die de opties laat zien als de widget is toegevoegd</a>
12. <a href="https://www.dropbox.com/s/8v86dvzbxo8nfz0/display_steemit_user_count.png?dl=0">Schermafbeelding van proefuitkomst Steemit info widget</a>
13. <a href="https://www.dropbox.com/s/ovg9zx5ex62ll5a/create_steemit_user_menu.png?dl=0">Schermafbeelding backend GK Steemit Info menu item</a>
14. <a href="https://www.dropbox.com/s/o2nds07etxxjkc6/create_steemit_user_interface.png?dl=0">Schermafbeelding van het nieuwe steemit gebruiker aanmaak scherm</a>
15. <a href="https://www.dropbox.com/s/klyt9a2101s7l0f/create_steemit_user_notification.png?dl=0">Weergeven meldingen</a>

## Changelog

### 0.5.0
- Toegevoegde ondersteuning voor het direct aanmaken van Steemit gebruikers via een back- end interface, waar een kleine delegatie van bestaande gebruikers nodig is.
- Kleur indicatoren toegevoegd voor SBD & STEEM prijswijzigingen in 1h,24h en 7d.
- Gerefabriceerde coinmarketcap code naar single functie.
- Gewijzigde referentie naar Coinmarketcap met smallere tekst uitnodigender ogend.
- Nieuwe schermafbeelding geüpload als vervanging voor bestaande Steemit Info widget gedateerde schermafbeelding.

### 0.4.0
- Nieuwe widget voor het scherm populaire berichten met optionele tag filters en limiet voor het aantal berichten.
- Extra informatie voor STEEM/SBD prijzen inclusief 24h en 7d veranderings- indicator voor Steemit info widget.
- Nieuwe real-time waarde berekening voor account waarde voor enkele gebuiker info widget.
- Verbeterde invoer voor berichten info (upvotes/beloningen).

### 0.3.0
- Nieuwe widget / shortcode gemaakt voor Steemit gebruikers informatie, inclusief naam, afbeelding, SP, STEEM, SBD, VP, Reputatie,... met backend selectievoor welke gebruiker info wordt weergegeven. Meerdere widgets worden ondersteund.
- Aangepaste bestaande Steemit info widget om nieuwe informatie toe te voegen, inclusief STEEM en SBD's huidige aanbod, maar nog belangrijker dat de actuele STEEM en SBD prijs rechtstreeks van coinmarketcap wordt gehaald, en wordt weergegeven als 1 uurs indicator, alswel de huidige plaats op de valuta ranglijst.

### 0.2.0
Ondersteuning toegevoegd voor Steemit Gebruikers- berichten widget, shortcode met filters en ondereteuning voor meerdere widgets.
Fix voor steemjs kwestie haperende functionaliteit tijdens de beweging naar api.steem.com.

### 0.1.0
Initiële Versie.
