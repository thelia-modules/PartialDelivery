Partial Delivery v1.0
=====================
author: Thelia <info@thelia.net>

Summary
-------

fr_FR:
1.  Installation
2.  Utilisation
3.  Boucle
4.  Intégration

en_US:
1.  Install notes
2.  How to use
3.  Loop
4.  Integration

fr_FR
-----

### Installation
Pour installer le mode Livraison partielle, téléchargez l'archive et décompressez la dans <dossier de Thelia>/local/modules

### Utilisation
Tout d'abord, activez le module en allant dans le back office, onglet Modules.

Vous pouvez maintenant gérer vos commandes en livraison partielle dans l'onglet "Modules" de votre commande.
Pour indiquer une livraison partielle, selectionnez la quantité envoyée de chaque produit, puis cliquez sur "Envoyer".

### Boucle

1.  partial.delivery.get.not.sent.products
    - Arguments:
        1. orderid | obligatoire | id de la commande
    - Sorties:
        Pour chaque produit partiellement/non envoyé de la commande:
        1. \$PRODUCT_REF: référence du produit en cours
        2. \$PRODUCT_ID: id du produit en cours
        3. \$PRODUCT_TITLE: Titre du produit en cours
        4. \$QTY: Quantité commandée du produit en cours
        5. \$SENT_QTY: Quantité déjà envoyée du produit en cours
    - Utilisation:
        ```{loop type="partial.delivery.get.not.sent.products" name="yourloopname" orderid=\$ID}
            <!-- your template -->
        {/loop}```

### Intégration
Un exemple d'intégration est proposé avec le thème par défault du Back-Office de Thélia,
il suffit de créer un formulaire sur la page de commande (order-edit.html) contenant les produits à envoyer avec la boucle
présentée ci-dessus.
De plus, vous pouvez modifier le template de mail pour convenir à vos besoins.

en_US
-----

### Install notes
To install this module, download the archive and uncompress it the directory <path to Thelia>/local/modules

### How to use
First, activate the module in your Back-Office, tab Modules.

You can now manage your partially sent orders on your order page, tab Modules.
To create a partial order, select the sent quantity of each product and click "Send".

### Loop

1.  partial.delivery.get.not.sent.products
    - Arguments:
        1. orderid | mandatory | id of the order
    - Output:
        Foreach partially/not sent product of the order:
        1. \$PRODUCT_REF: reference of the current product
        2. \$PRODUCT_ID: id of the current product
        3. \$PRODUCT_TITLE: Title of the current product
        4. \$QTY: Ordered quantity of the current product
        5. \$SENT_QTY: Already sent quantity of the current product
    - Usage:
        ```{loop type="partial.delivery.get.not.sent.products" name="yourloopname" orderid=\$ID}
            <!-- your template -->
        {/loop}```

### Integration
An integration example is available for Thelia's default back-office template.
you only have to create a form on order edit page (order-edit.html), containing the products given by the loop
partial.delivery.get.not.sent.products.
Moreover, you can edit the mail template to complete it to your needing.