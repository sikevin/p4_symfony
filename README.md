# p4_symfony
Création d'un nouveau système de réservation et de gestion des tickets en ligne

## CDC
Appli responsive design

Interface fonctionnelle claire et rapide: l’objectif est de permettre aux visiteurs d’acheter un billet rapidement -> Formulaire JQuery / plusieurs formulaires

__Types de billets:__
* Billet « journée » 
* Billet « Demi-journée « Permet de rentrer qu’à partir de 14h »

Musée ouvert tous les jours sauf mardi (fermé le 1er mai, 1er novembre et 25 décembre)

__Types de tarifs:__
* Tarifs « normal » 16€  >= 12 ans
* Tarifs « enfant »  à partir de 4 ans jusqu’à 12 ans 8€
* Tarifs « senior » 12€ >= 60 ans
* Tarifs « réduit » 10€ -> étudiant / employé musée, service du ministère de la culture, militaire, handicapé, groupes…

__Pour commander, sélectionner:__
* Le jour de la visite (calendrier)
* Le type de billet (journée, demi-journée..)
* Le nombre de billets souhaités

On peux commander un billet le jour même mais on ne peut plus commander de billet « Journée » une fois 14h00 passées.

__Impossible de réserver pour:__
* les journées passés
* les dimanches
* les jours fériés
* les jours où nb de billets vendus > 1000

__Pour chaque billet l’user doit préciser:__
* son nom
* son prénom
* son pays
* sa date de naissance -> déterminera le tarif du billet 

__Quelques Contraintes en plus:__
- [ ] Une case à coché en plus pour les tarifs réduits ( en indiquant qu’il est nécessaire de présenter sa carte d’étudiant, militaire, ou équivalent pour prouver qu’on bénéficie bien du tarif réduit).

- [ ] Le site récupérera par ailleurs l’e-mail du visiteur afin de lui envoyer les billets. 

- [ ] Pas besoin de compte utilisateur pour commander.

- [ ] Le visiteur doit pouvoir payer avec la solution Stripe car carte bancaire.

- [ ] Gérer les retour du paiement. En cas d’erreur, il invite à recommencer l’opération.
 
- [ ] Si tout s’est bien passé, la commande est enregistrée et les billets sont envoyés au visiteur.

- [ ] Utiliser les environnements de test fournis par Stripe pour simuler la transaction.

La création d’un back-office pour lister les clients et commander n’est pas demandée. Seul l’interface client est nécessaire ici.


### Le billet

Un email de confirmation sera envoyé à l’utilisateur et fera foi de billet.

Le mail doit indiquer:
	* Le nom et le logo du musée
	* La date de réservation
	* Le tarif
	* Le nom de chaque visiteur
	* Le code de la réservation (un ensemble de lettres et de chiffres)

### Livrables attendus

* Document de présentation de la solution pour le client, incluant la note de cadrage (PDF)
* Code source complet du projet versionné avec Git, développé avec le framework PHP Symfony
* Quelques (4-5) tests unitaires et fonctionnels que l’on peut exécuter