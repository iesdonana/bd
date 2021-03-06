drop table if exists localidades cascade;

create table localidades (
    id        bigserial constraint pk_localidades primary key,
    loc       varchar(100) not null
);

insert into localidades (loc)
    values ('SANLÚCAR'),
           ('JEREZ'),
           ('TREBUJENA'),
           ('CHIPIONA');

drop table if exists depart cascade;

create table depart (
    dept_no      numeric(2)  constraint pk_depart primary key,
    dnombre      varchar(20) not null,
    localidad_id bigint      constraint fk_depart_localidades
                             references localidades (id)
                             on delete set null on update cascade
);

insert into depart (dept_no, dnombre, localidad_id)
    values (10, 'CONTABILIDAD', 1),
           (20, 'INVESTIGACIÓN', 2),
           (30, 'VENTAS', 3),
           (40, 'PRODUCCIÓN', 4);

drop view if exists depart_v cascade;

create view depart_v as
    select *
    from depart d left join localidades l on d.localidad_id = l.id;

drop table if exists usuarios cascade;

create table usuarios (
    id     bigserial    constraint pk_usuarios primary key,
    nombre varchar(20)  not null constraint uq_usuarios_nombre unique,
    pass   varchar(255) not null
);

insert into usuarios (nombre, pass)
    values ('pepe', crypt('pepe', gen_salt('bf', 10))),
           ('juan', crypt('juan', gen_salt('bf', 10)));

drop table if exists fichas cascade;

create table fichas (
    id     bigserial    constraint pk_fichas primary key,
    titulo varchar(255) not null
);

insert into fichas (titulo)
    values ('La bala que dobló la esquina'),
           ('Dos pistolas para un manco'),
           ('Ikuku y la botella'),
           ('Vente a Alemania Pepe'),
           ('Los bingueros'),
           ('La muerte de los inmortales');
