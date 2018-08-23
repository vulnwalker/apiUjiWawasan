CREATE TABLE admin
      (
        `uid` varchar(20) COLLATE latin1_general_ci  NOT NULL DEFAULT '' , 
`nama` varchar(50) COLLATE latin1_general_ci  NOT NULL DEFAULT '' , 
`password` varchar(32) COLLATE latin1_general_ci  NOT NULL DEFAULT '' , 
`level` char(1) COLLATE latin1_general_ci   DEFAULT '2' , 
`group` char(15) COLLATE latin1_general_ci   DEFAULT '' , 
`modul01` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`modul02` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`modul03` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`modul04` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`modul05` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`modul06` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`modul07` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`modul08` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`modul09` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`modul10` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`modul11` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`modul12` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`modul13` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`modul14` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`modul15` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`modul16` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`modulref` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`moduladm` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`status` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`online` char(1) COLLATE latin1_general_ci   DEFAULT '0' , 
`lastaktif` datetime    DEFAULT '' , 
`photo` text COLLATE latin1_general_ci   DEFAULT '' , 
`ipaddr` varchar(50) COLLATE latin1_general_ci   DEFAULT '' , 
`sesino` int(11)    DEFAULT '' 
      );

      