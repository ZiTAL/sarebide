# SAREBIDE - BLOCKHAIN #

## REQUIREMENTS ##

- NOHUP
- PHP
- NODE
- PANDOC

## CREATE UPLOAD FOLDERS ##

Create a each folder per node

```
mkdir -p upload/nodes/node01
mkdir -p upload/nodes/node02
...
```

## CREATE TEMPORAL FOLDER ##

```
mkdir tmp
```

## CREATE CERTIFIED FOLDER ##

```
mkdir certified
```

## INSTALL BLOCKCHAIN DEPENDENCIES ##

```
cd blockchain
npm install
```

## EDIT BLOCKCHAIN SERVERS ##

Edit servers script to replace the url of nodes:

```
cd blockchain
nano server-01.sh
nano server-02.sh
```

## RUN BLOCKCHAIN SERVERS ##

```
cd blockchain
nohup server-01.sh
nohup server-02.sh
```

## CSV EXAMPLES ##

There are CSV examples in CSV folder.

## CREATE A CRON ##

Cron is needed to generate certifieds.

```
* * * * * /usr/bin/php /home/projects/sarebide/generate.php >>/dev/null 2>> /home/projects/sarebide/generate.log
```

## VIDEO ##

Video explaining how to use the blockchain:

https://github.com/ZiTAL/sarebide/blob/master/safrie_albacora_pevasa.mp4?raw=true
