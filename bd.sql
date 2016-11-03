drop table if exists depart cascade;

create table depart (
    dept_no numeric(2)  constraint pk_depart primary key,
    dnombre varchar(20) not null,
    loc     varchar(50)
);

insert into depart
    values (10, 'CONTABILIDAD','SANLUCAR'),
           (20, 'INVESTIGACION','JEREZ'),
           (30, 'VENTAS','TREBUJENA'),
           (40, 'PRODUCCION','CHIPIONA');
