<p align="center"><a href="https://www.wpshop.fr"><img src="https://www.eoxia.com/wp-content/uploads/2018/11/logo-final-max.png" alt="WPshop"></a></p>

<p align="center">
  <a href="https://travis-ci.org/Eoxia/wpshop"><img src="https://travis-ci.org/Eoxia/wpshop.svg?branch=2.0.0" alt="Build Status"></a>
  <a href="https://scrutinizer-ci.com/g/Eoxia/wpshop/?branch=2.0.0"><img src="https://scrutinizer-ci.com/g/Eoxia/wpshop/badges/quality-score.png?b=2.0.0" /></a>
</p>

## Guide d'installation
- Installer Dolibarr
- Activer les **modules** de Dolibarr :
	-  Tiers
	- Propositions commerciales
	- Commandes client
	-  Factures et avoirs
	- Produits
	- Services
	- Stock
	- API/Web Services (serveur REST)
	- Paypal
- Dans l'onglet **Super Admin**, générer une clé API
- Dans l'onglet **Configuration** -> **Divers**, ajouter une ligne avec, comme données :
	- Nom : PRODUCT_PRICE_UNIQ
	- Valeur : 1
- Installer votre WordPress
- Installer et activer l'extension WPshop v2.x.x
- Dans les options de WPshop, Ajouter la clé API précédemment générée dans l'onglet "Dolibarr Secret"
