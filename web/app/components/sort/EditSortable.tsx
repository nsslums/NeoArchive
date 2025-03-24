import { UniqueIdentifier } from "@dnd-kit/core";
import { useSortable } from "@dnd-kit/sortable";
import { CSS } from "@dnd-kit/utilities";
import { CiMenuBurger } from "react-icons/ci";
import { Card } from "../ui/card";

export type SortableProps = {
  children: React.ReactNode;
  id: UniqueIdentifier;
  className?: string;
  onClick?: () => void;
};

export function EditSortable({ children, id, className, onClick }: SortableProps) {
  const {
    attributes,
    listeners,
    setNodeRef,
    transform,
    transition,
    isDragging,
  } = useSortable({ id });

  const style = {
    transform: CSS.Transform.toString(transform),
    transition,
    backgroundColor: isDragging ? "green" : undefined,
  };

  return (
    <Card
      className={`${className} flex flex-row`}
      ref={setNodeRef}
      style={style}
      {...attributes}
      onClick={onClick}
    >
      <div {...listeners} className="px-3 flex items-center">
        <CiMenuBurger size={15} />
      </div>
      <div className="flex-1 my-2 mr-2">{children}</div>
    </Card>
  );
}
