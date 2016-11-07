drop table if exists localidades cascade;

create table localidades (
    id  bigserial    constraint pk_localidades primary key,
    loc varchar(100) not null
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
