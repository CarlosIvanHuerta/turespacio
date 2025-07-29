create table publicidad
(
    id                int auto_increment primary key,
    url_externa       varchar(500)  null comment 'hipervinculo externo',
    path_img          varchar(300)  null comment 'ruta de imagen',
    text_important    varchar(30)   null comment 'texto a resaltar',
    clicks_publicidad int default 0 not null comment 'eventos declicks',
    estatus           smallint      null comment '1 activo 2 inactivo'
)
    comment 'tabla para registrar imagenes de publicidad';